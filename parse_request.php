<?php

use Sabre\HTTP;

require_once 'vendor/autoload.php';

function parse_request(HTTP\RequestInterface $request, HTTP\ResponseInterface $response, $data)
{
    // fix for javascript fetch
    // https://stackoverflow.com/questions/36669911/post-not-retrieving-data-from-javascripts-fetch
    try {
        $request_body = (array) json_decode($request->getBodyAsString());
    } catch (\Throwable $th) {
        $request_body = [];
    } finally {
        $post = array_merge($request->getPostData(), $request_body);
    }

    // parse POST arguments
    $required_post = ['subject', 'to', 'message', 'token'];
    foreach ($required_post as $field) {
        if (!isset($post[$field])) {
            return_error($request, $response, "HTTP POST field '${field}' is missing");
        }
        if (empty($post[$field])) {
            return_error($request, $response, "HTTP POST field '${field}' is empty");
        }
    }

    // validate token
    if ($data['token'] !== $post['token']) {
        return_error($request, $response, "Your token is invalid");
    }

    $data['subject'] = $post['subject'];

    $post['to'] = count(explode(',', $post['to'])) == 1 ? $post['to'] : explode(',', $post['to']);

    if (is_string($post['to'])) {
        $data['to'] = isset($post['to_name']) && !empty($post['to_name']) ?
        [$post['to'] => $post['to_name']] :
        [$post['to']];
    } else if (is_array($post['to'])) {
        $data['to'] = $post['to'];
    } else {
        return_error($request, $response, "Invalid type for 'to' field. Send single recipient or array of recipients");
    }

    $data['from'] = isset($post['from_name']) && !empty($post['from_name']) ?
    [$data['from_email'] => $post['from_name']] :
    [$data['from_email'] => $data['from_name']];

    $data['message'] = strpos($post['message'], 'base64:') === 0 ? base64_decode(ltrim($post['message'], 'base64:')) : $post['message'];

    if (is_array($post['attachments'])) {
        $data['attachments'] = $post['attachments'];
    }

    return $data;
}
