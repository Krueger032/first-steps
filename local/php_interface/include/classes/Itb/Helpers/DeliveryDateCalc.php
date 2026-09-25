<?php
namespace Itb\Helpers;

class DeliveryDateCalc
{
    public static function deliveryDate()
    {
        $currentDateTime = new \DateTime();
//         $currentDateTime = new \DateTime('2025-09-11 10:00');
//         $currentDateTime = new \DateTime('2025-09-12 10:00'); // Пятница до 14:00
//         $currentDateTime = new \DateTime('2025-09-12 15:00'); // Пятница после 14:00

        $currentHour = (int)$currentDateTime->format('H');
        $currentDayOfWeek = $currentDateTime->format('N');

        $daysToAdd = 1;

        if ($currentHour >= 14) {
            $daysToAdd = 2;
        }

        if ($currentDayOfWeek == 5) {
            if ($currentHour < 14) {
                $daysToAdd = 3;
            } else {
                $daysToAdd = 4;
            }
        }

        $deliveryDate = clone $currentDateTime;
        $deliveryDate->modify("+{$daysToAdd} days");

//        for ($i = 0; $i < $daysToAdd; $i++) {
//            $deliveryDate->modify('+1 day');
//
//            while (in_array($deliveryDate->format('N'), [6, 7])) {
//                $deliveryDate->modify('+1 day');
//            }
//        }

        while (in_array($deliveryDate->format('N'), [6, 7])) {
            $deliveryDate->modify('+1 day');
        }

        return self::formatRussianDate($deliveryDate);
    }

    private static function formatRussianDate($date)
    {
        $months = [
            1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
            5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
            9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
        ];

        $day = (int)$date->format('d');
        $month = $months[(int)$date->format('m')];

        return "С {$day} {$month}";
    }
}
