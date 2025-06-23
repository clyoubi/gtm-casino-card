<?php
defined('ABSPATH') || exit;

if (!class_exists('Casino')) {
    class Casino
    {
        public string $id;
        public string $name;
        public string $logo_url;
        public float $average_rtp;
        public float $biggest_win_month;
        public int $payment_delay_hours;
        public float $monthly_withdrawal_limit;
        public float $validated_withdrawals_value;
        public int $monthly_withdrawals_number;
        public static $currencies = ['EUR' => '€', 'USD' => '$'];
        //TODO: Missing the go link in the API provided by the Backend Team, should be added to have an active CTA
        public string $go;

        public function __construct(array $data)
        {
            $this->id                          = $data['id'] ?? '';
            $this->name                        = $data['name'] ?? '';
            $this->logo_url                    = $data['logo_url'] ?? '';
            $this->average_rtp                 = (float) ($data['average_rtp'] ?? 0);
            $this->biggest_win_month           = (float) ($data['biggest_win_month'] ?? 0);
            $this->payment_delay_hours         = (int) ($data['payment_delay_hours'] ?? 0);
            $this->monthly_withdrawal_limit    = (float) ($data['monthly_withdrawal_limit'] ?? 0);
            $this->validated_withdrawals_value = (float) ($data['validated_withdrawals_value'] ?? 0);
            $this->monthly_withdrawals_number  = (int) ($data['monthly_withdrawals_number'] ?? 0);
            $this->go                          = $data['go'] ?? '#';
        }

        public static function moneyFormat(float $amount)
        {
            $currency = get_option('casino_general_currency', 'EUR');
            printf(
                '%s %s',
                esc_html(number_format($amount)),
                esc_html(self::mapCurrencyToCode($currency))
            );
        }

        public static function mapCurrencyToCode(string $currency): string
        {
            return self::$currencies[$currency] ?? '';
        }
    }
}
