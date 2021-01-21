<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class PushNotificationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testPushNotification()
    {
        // $testDeviceId = ["c42e96e0-a8fd-4d6e-bd27-9c1c66e496fb"];
       // $testDeviceId = ["c42e96e0-a8fd-4d6e-bd27-9c1c66e496fb"];
       $player_id = ["00f827ef-4c8d-46d4-90d1-b10dfa096870"];
       $id =12;
        $type = 'pending';
        $name = "neha bhole";
        $inviter_name = "test user";
        $msg_data = [
          'invited_user_name' => $name,
          'user_invited_by' => $inviter_name,
        ];
        $message_name = 'added_to_group_notification_message';
        $msg = get_message_text($message_name, $msg_data);
        // $msg = "Hi ".$name.", ".$inviter_name." just added you to their crew on UnstuQ. Come join the excitement and see what the buzz is all about. Download the app here. https://unstuq.com";
        $response = sendMessage($testDeviceId, $msg,$pending,$id);
      //  $response = sendPushNotification($testDeviceId, $msg);
        Log::info("testPushNotification : ".$response);

        $this->assertTrue($response);
    }
}
