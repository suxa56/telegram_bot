<?php

namespace App\Http\Controllers;

use App\Constants\CallbackData;
use App\Constants\Languages;
use App\Http\service\HttpService;
use App\Models\InlineButton;
use App\Models\UpdateTG;
use App\Models\Users;

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

        $requestButton = new InlineButton($request, CallbackData::HOME_MUROJAAT);
        $corruptionButton = new InlineButton($corruption, CallbackData::HOME_ANTICOR);
        $languageButton = new InlineButton($language, CallbackData::HOME_LANGUAGE);
        $murojaatAdminButton = new InlineButton($incomeMurojaat, CallbackData::INCOME_MUROJAAT);
        $anticorAdminButton = new InlineButton($incomeAnticor, CallbackData::INCOME_ANTICOR);

        $this->httpService->sendMessage(
            $user->chat_id,
            $text,
            [
                [$requestButton->toArray()],
                [$corruptionButton->toArray()],
                [$languageButton->toArray()],
                $user->is_admin || $user->is_murojaat ? [$murojaatAdminButton->toArray()] : [],
                $user->is_admin || $user->is_anticor ? [$anticorAdminButton->toArray()] : [],
            ]
        );
    }
}
