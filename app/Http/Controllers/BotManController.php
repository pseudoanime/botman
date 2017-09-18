<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\ExampleConversation;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->hears('foo', function ($bot) {
            $bot->reply('Hello World');
        });

        $botman->hears('more', function (BotMan $bot) {
            $bot->reply("Tell me more!");
            $bot->reply("And even more");
        });

        $botman->hears('call me {name}', function ($bot, $name) {
            $bot->reply('Your name is: ' . $name);
        });

        $botman->hears('image', function (BotMan $bot) {
            $attachment = new Image('https://www.techspot.com/images2/downloads/topdownload/2014/05/twitter.jpg', [
                'custom_payload' => true,
            ]);

            // Build message object
            $message = OutgoingMessage::create('This is my text')
                ->withAttachment($attachment);

            // Reply message object
            $bot->reply($message);

        });

        $botman->hears('I want ([0-9]+)', function ($bot, $number) {
            $bot->reply('You will get: ' . $number);
        });

        $botman->group(['driver' => SlackDriver::class], function ($bot) {
            $bot->hears('keyword', function ($bot) {
                // Only listens on Slack
            });
        });

        $botman->group(['recipient' => '1234567890'], function ($bot) {
            $bot->hears('keyword', function ($bot) {
                // Only listens when recipient '1234567890' is sending the message.
            });
        });

        $botman->hears('wait', function (BotMan $bot) {
            $bot->typesAndWaits(2);
            $bot->reply("Tell me more!");
        });

        $botman->fallback(function ($bot) {
            $bot->reply('Sorry, I did not understand these commands. Here is a list of commands I understand: ...');
        });

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     *
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }
}
