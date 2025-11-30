<?php
require_once __DIR__ . "/../classes/chat_class.php";

function send_message_ctr($order_id, $sender_id, $receiver_id, $message) {
    $chat = new Chat();
    return $chat->sendMessage($order_id, $sender_id, $receiver_id, $message);
}

function get_messages_ctr($order_id) {
    $chat = new Chat();
    return $chat->getMessages($order_id);
}

function mark_read_ctr($order_id, $receiver_id) {
    $chat = new Chat();
    return $chat->markRead($order_id, $receiver_id);
}

function get_unread_count_ctr($receiver_id) {
    $chat = new Chat();
    return $chat->getUnreadCount($receiver_id);
}
?>
