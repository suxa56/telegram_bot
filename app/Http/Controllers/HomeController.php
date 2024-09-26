<?php

namespace App\Http\Controllers;

use App\Http\service\HttpService;
use App\Models\CallbackData;
use App\Models\Languages;
use App\Models\UpdateTG;
use App\Models\Users;
use Illuminate\Support\Facades\Http;

class HomeController
{
    private HttpService $httpService;

    /**
     * @param HttpService $httpService
     */
    public function __construct(HttpService $httpService)
    {
        $this->httpService = $httpService;
    }

    /**
     * @param Users $user
     * @param UpdateTG|null $update
     * @return void
     */
    public function index(Users $user, ?UpdateTG $update): void
    {
        if ($user->language == Languages::RU) {
            $text = '👋 Привет! Вы находитесь в официальном боте Узбек Гидро Энерго.

Здесь вы можете:

📩 Обратиться с предложением или вопросом — мы всегда готовы рассмотреть ваши идеи и помочь с любыми вопросами.
⚖️ Сообщить о коррупции — если вы столкнулись с неправомерными действиями, пожалуйста, дайте нам знать. Ваше обращение останется конфиденциальным.
🌐 Сменить язык — выберите язык бота.

Чтобы начать, просто выберите нужный пункт из меню ниже.';

            $request = '📩 Предложение или вопрос';
            $corruption = '⚖️ Сообщить о коррупции';
            $language = '🌐 Сменить язык';
            $incomeMurojaat = '📥 Поступившие предложения';
            $incomeAnticor = '⚠️ Поступившие жалобы';
        } elseif ($user->language == Languages::UZ) {
            $text = '👋 Salom! Siz O\'zbek Gidro Energo kompaniyasining rasmiy botidasiz.

Bu yerda siz:

📩 Taklif yoki savol bo\'yicha biz bilan bog\'lanishingiz mumkin - biz sizning taklifingizni ko\'rib chiqishga va har qanday savol bo\'yicha yordam berishga doim tayyormiz.
⚖️ Korrupsiya haqida xabar berishingiz mumkin - agar siz noto\'g\'ri xatti-harakatlarga duch kelsangiz, bizga xabar bering. Sizning murojaatingiz maxfiy saqlanib, ko\'rib chiqiladi.
🌐 Tilni o\'zgartirish - bot tilini tanlang.

Boshlash uchun quyidagi menyudan biror bandni tanlang.';

            $request = '📩 Taklif yoki savol';
            $corruption = '⚖️ Korrupsiya haqida xabar berish';
            $language = '🌐 Tilni o\'zgartirish';
            $incomeMurojaat = '📥 Kelgan takliflar';
            $incomeAnticor = '⚠️ Kelgan shikoyatlar';
        } else {
            $text = '👋 Hello! You are in the official bot of Uzbek Hydro Energy.

Here you can:

📩 Contact us with a suggestion or question - we are always ready to consider your ideas and help with any questions.
⚖️ Report Corruption - If you experience misconduct, please let us know. Your request will remain confidential.
🌐 Change language - select bot language.

To get started, simply select an item from the menu below.';

            $request = '📩 Suggestion or question';
            $corruption = '⚖️ Report Corruption';
            $language = '🌐 Change language';
            $incomeMurojaat = '📥 Received suggestions';
            $incomeAnticor = '⚠️ Received complaints';
        }

        if (isset($update->callbackQuery->id)) {
            $this->httpService->reactToCallback($update);
        }

        Http::post('https://api.telegram.org/bot7849210506:AAHwUp5nF6nWxxfEoEH8NVBP6CwyRtHUx7s/sendMessage', [
            'chat_id' => $user->chat_id,
            'text' => $text,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => $request, 'callback_data' => CallbackData::HOME_MUROJAAT]],
                    [['text' => $corruption, 'callback_data' => CallbackData::HOME_ANTICOR]],
                    [['text' => $language, 'callback_data' => CallbackData::HOME_LANGUAGE]],
                    $user->is_admin || $user->is_murojaat ? [['text' => $incomeMurojaat, 'callback_data' => CallbackData::INCOME_MUROJAAT]] : [],
                    $user->is_admin || $user->is_anticor ? [['text' => $incomeAnticor, 'callback_data' => CallbackData::INCOME_ANTICOR]] : [],
                ]
            ]),
        ]);
    }
}
