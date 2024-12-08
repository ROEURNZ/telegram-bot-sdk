<?php

function sendMessage($params, $method = null, $log = true, $useCA = false)
{
    global $api_key, $ezzeTeamsModel, $adminDetils, $logID;

    $method = isset($method) ? $method : 'sendMessage';
    $url = 'https://api.telegram.org/bot' . $api_key . '/' . $method;

    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($handle, CURLOPT_TIMEOUT, 120);
    curl_setopt($handle, CURLOPT_VERBOSE, true);
    curl_setopt($handle, CURLOPT_DNS_SERVERS, "8.8.8.8");
    curl_setopt($handle, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    if ($useCA) {
        curl_setopt($handle, CURLOPT_CAINFO, __DIR__ . "/../../../cacert/cacert.pem");
    }
    //curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($params));
    if ($method == 'sendDocument') {
        $finfo = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $params['document']);
        $cFile = new CURLFile(realpath($params['document']), $finfo);

        // Add CURLFile to CURL request
        $params['document'] = $cFile;
        curl_setopt($handle, CURLOPT_POSTFIELDS, $params);
    } else {
        curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($params));
    }
    $response = curl_exec($handle);

    if ($response === false) {
        $errorNumber = curl_errno($handle);
        $errorMessage = curl_error($handle);
        curl_close($handle);

        if (!$useCA) {
            sleep(1);
            return sendMessage($params, $method, $log, true);
        }

        // Display the error
        $msg = "CURL ERR: " . $errorNumber . " | " . $errorMessage . " | " . json_encode($params);
        syslog(LOG_ERR, $msg);
        sendLogMessage($msg);
        return false;
    }

    if (isset($logID)) {
        $ezzeTeamsModel->logReply($logID, $url, $params, $response);
    } else {
        $ezzeTeamsModel->requestLog($url, $params, $response);
    }

    $response = json_decode($response);
    if (isset($response->result->text)) {
        $msg = $response->result->text;

        if ($response->result->text == getReceivedNewApplicationNotificationTxt()) {
            $ezzeTeamsModel->updateUserNotificationNewUserMSGID($response->result->message_id, $response->result->chat->id);
        } else if (strpos($response->result->text, 'List of Employee') !== false) {
            $ezzeTeamsModel->updateUserListEmpMSGID($response->result->message_id, $response->result->chat->id);
        } else if (strpos($response->result->text, 'List of Active Employee') !== false) {
            $ezzeTeamsModel->updateUserListEmpMSGID($response->result->message_id, $response->result->chat->id);
        } else if (strpos($response->result->text, 'Day selected') !== false) {
            $ezzeTeamsModel->updateUserDaySelectedMSGID($response->result->message_id, $response->result->chat->id);
        } else if (strpos($response->result->text, 'Working Time For') !== false) {
            $ezzeTeamsModel->updateUserStartTimeMSGID($response->result->message_id, $response->result->chat->id);
        } else if (strpos($response->result->text, 'Please set end time') !== false) {
            $ezzeTeamsModel->updateUserEndTimeMSGID($response->result->message_id, $response->result->chat->id);
        } else if (strpos($response->result->text, 'Scheduled Message Repeat Configurations') !== false) {
            $adminUser = $ezzeTeamsModel->getAdminStep($response->result->chat->id);
            $tempData = json_decode($adminUser['temp'], TRUE);
            $tempData['msgEditId'] = $response->result->message_id;
            $ezzeTeamsModel->setAdminStep($response->result->chat->id, $adminUser['step'], json_encode($tempData));
        } else if (strpos($response->result->text, 'List Scheduled Messages') !== false) {
            $ezzeTeamsModel->updateUserListEmpMSGID($response->result->message_id, $response->result->chat->id);
        }
    }
    return $response;
}
