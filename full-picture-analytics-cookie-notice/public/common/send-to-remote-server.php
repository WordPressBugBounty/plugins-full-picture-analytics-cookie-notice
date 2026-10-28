<?php

// Caller provides $requests_a (a list of requests) and $responses = [].
// This include fills $fupi_remote_delivery_results, $responses, and $logged_responses.

// Initialize a multi-cURL handle
$multiCurl      = curl_multi_init();
$curlHandles    = [];
$logged_responses = [];
$fupi_remote_delivery_results = [];

foreach ( $requests_a as $request_index => $request ) {

    // $request = [
    //   'url' => 'https://example.com/api1',
    //   'headers' => ['Content-Type: application/json'],
    //   'payload' => ['key1' => 'value1', 'key2' => 'value2'],
    //   'tool' => 'example', // optional identifier for WooCommerce delivery tracking
    //   'return_response' => 'CDB', // optional: false (default), 'debug.log', or a response label
    //                              // labeled bodies go into $responses; debug.log bodies are logged below
    // ];
    
    // trigger_error( 'Preparing to send request: ' . json_encode( $request ) );
    
    $invalid_reason = false;

    if (
        ! is_array( $request ) ||
        ! isset( $request['url'], $request['headers'], $request['payload'] ) ||
        ! is_string( $request['url'] ) || trim( $request['url'] ) === '' ||
        ! is_array( $request['headers'] ) ||
        ! ( is_array( $request['payload'] ) || is_object( $request['payload'] ) )
    ) {
        $invalid_reason = 'Invalid request fields';
    } else {
        foreach ( $request['headers'] as $header ) {
            if ( ! is_string( $header ) ) {
                $invalid_reason = 'Invalid request headers';
                break;
            }
        }

        if ( ! $invalid_reason ) {
            $encoded_payload = json_encode( $request['payload'] );
            if ( $encoded_payload === false ) $invalid_reason = 'JSON encoding failed: ' . json_last_error_msg();
        }
    }

    if ( $invalid_reason ) {
        // Keep preparation failures in the same result format as failed transfers.
        $fupi_remote_delivery_results[ $request_index ] = [
            'request_index' => $request_index,
            'tool' => is_array( $request ) ? ( $request['tool'] ?? false ) : false,
            'http_code' => 0,
            'curl_errno' => CURLE_BAD_FUNCTION_ARGUMENT,
            'curl_error' => $invalid_reason,
        ];
        continue;
    }

    // Initialize a new cURL session
    $ch = curl_init();

    // Set cURL options for the POST request
    curl_setopt($ch, CURLOPT_URL, $request['url']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request['headers']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_payload );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set a timeout for the request (in seconds)
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Add connection timeout (in seconds)

    // Add this cURL handle to the multi-cURL handle
    $add_status = curl_multi_add_handle($multiCurl, $ch);
    if ( $add_status !== CURLM_OK ) {
        $fupi_remote_delivery_results[ $request_index ] = [
            'request_index' => $request_index,
            'tool' => $request['tool'] ?? false,
            'http_code' => 0,
            'curl_errno' => CURLE_FAILED_INIT,
            'curl_error' => 'Could not add request to multi-cURL: ' . curl_multi_strerror( $add_status ),
        ];
        curl_close( $ch );
        continue;
    }

    // Keep track of this handle
    $curlHandles[] = [
        'handle' => $ch,
        'request_index' => $request_index,
        'tool' => isset( $request['tool'] ) ? $request['tool'] : false,
        'return_response' => empty( $request['return_response'] ) ? false : $request['return_response']
    ];
}

// Execute all requests asynchronously
$running = null;

do {
    $multi_status = curl_multi_exec( $multiCurl, $running );
    if ( $multi_status !== CURLM_OK ) break;

    if ( $running > 0 && curl_multi_select( $multiCurl ) === -1 ) {
        usleep( 100000 );
    }
} while ( $running > 0 );

$multi_results = [];
while ( $multi_info = curl_multi_info_read( $multiCurl ) ) {
    if ( $multi_info['msg'] === CURLMSG_DONE ) {
        $multi_results[ spl_object_id( $multi_info['handle'] ) ] = $multi_info['result'];
    }
}

// Collect responses from each handle

foreach ( $curlHandles as $handle_data) {

    $handle_id = spl_object_id( $handle_data['handle'] );
    $completed = isset( $multi_results[ $handle_id ] );
    $curl_result = $completed ? $multi_results[ $handle_id ] : CURLE_FAILED_INIT;
    $curl_error = $completed
        ? ( $curl_result === CURLE_OK ? '' : curl_strerror( $curl_result ) )
        : ( $multi_status === CURLM_OK ? 'Transfer did not complete' : 'Multi-cURL execution failed: ' . curl_multi_strerror( $multi_status ) );

    $fupi_remote_delivery_results[ $handle_data['request_index'] ] = [
        'request_index' => $handle_data['request_index'],
        'tool' => $handle_data['tool'],
        'http_code' => curl_getinfo( $handle_data['handle'], CURLINFO_RESPONSE_CODE ),
        'curl_errno' => $curl_result,
        'curl_error' => $curl_error,
    ];

    if ( ! empty( $handle_data['return_response'] ) ) {
        
        $response = curl_multi_getcontent( $handle_data['handle'] );
        
        if ( $handle_data['return_response'] == 'debug.log' ) {
            $logged_responses[] = is_string( $response ) && strlen( $response ) > 4096
                ? substr( $response, 0, 4096 ) . ' [truncated]'
                : $response;
        } else {
            $responses[ $handle_data['return_response'] ] = $response;
        }
    };
    curl_multi_remove_handle($multiCurl, $handle_data['handle']);
    curl_close($handle_data['handle']);
}

ksort( $fupi_remote_delivery_results );
$fupi_remote_delivery_results = array_values( $fupi_remote_delivery_results );

// Close the multi-cURL handle
curl_multi_close( $multiCurl );

if ( count( $logged_responses ) > 0 ) {
    trigger_error( json_encode( $logged_responses, JSON_INVALID_UTF8_SUBSTITUTE ) );
}
