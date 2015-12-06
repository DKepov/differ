<?php

/**
 * Class Diff
 *
 * Сравнение двух текстовых строк
 *
 */
class Diff
{

    /**
     * Основная функция для сравнения двух текстов
     *
     * Основная функция для вызова сравнения двух текстов
     *
     * Возвращает текст, в котором все строки посвечены, особым образом
     *
     * @param $o
     * @param $n
     *
     * @return string
     */
    public static function compare($o, $n)
    {
        // разбиваем текста на массивы по строкам
        $oArr = preg_split('/\\r\\n?|\\n/', $o);
        $nArr = preg_split('/\\r\\n?|\\n/', $n);

        // получаем для каждой строки хэш и общий хэш-массив
        list($hashStrArr, $oArrHash, $nArrHash) = self::hasher($oArr, $nArr);

        // test возвращаем массивы после хэширования
        //return print_r([$oArrHash, $nArrHash], true);

        // вызываем поиск различий
        $diffArrHash = self::differ($oArrHash, $nArrHash);

        // test выводим массив разницы
        //return print_r($diffArrHash, true);

        // визуализируем различия
        $vizualText = self::visualizer($diffArrHash, $hashStrArr);

        // собственно возвращаем результат
        return $vizualText;
    }

    /**
     * Хэшируем входящий массивы
     *
     * Хэшируем входящие массивы, добавляем все возможные строки из обоих массивов в общий словарь.
     * Возвращаем массив из трех элементов.
     * Первый элемент словарь, второй массив и третий массив - ссылки на значения в словаре, согласно позиции элемента.
     *
     * Хэшируем входящий массивы, ключами значений элемента становится хэш от самого значения элемента
     *
     * @param $oArr
     * @param $nArr
     *
     * @return array
     *
     */
    private static function hasher($oArr, $nArr)
    {
        $hashStrArr = [];
        $oArrHash = [];
        $nArrHash = [];

        foreach ($oArr as $key => $value)
        {
            $keyHash = sha1($value);
            $oArrHash[] = $keyHash;
            $hashStrArr[$keyHash] = $value;
        }

        foreach ($nArr as $key => $value)
        {
            $keyHash = sha1($value);
            $nArrHash[] = $keyHash;
            $hashStrArr[$keyHash] = $value;
        }

        return [$hashStrArr, $oArrHash, $nArrHash];
    }

    /**
     * Поиск различий среди двух массивов
     *
     * Поиск различий среди двух последовательностей, методом LCS.
     * Поиск всевозможных добавлений, удалений, изменений.
     *
     * Результирующий массив это двухуровневый массив, ключем которого является строчка, а значением
     * массив из двух элементов, в котором:
     * 1) первый элемент пустой, а второй с хэшем - было добавление строки
     * 2) первый элемент с хэшем, а второй пустой - было удаление строки
     * 3) первый и второй элемент с хэшем - была замена строк
     *
     * @param $oArrHash
     * @param $nArrHash
     *
     * @return array
     */
    private static function differ($oArrHash, $nArrHash)
    {
        // находим общую подпоследовательность
        $jointArrHash = self::LCSAlgorithm($oArrHash, $nArrHash);

        // используем общую подпоследовательность для нахождения в массивх разниц
        $difference = self::difference($oArrHash, $nArrHash, $jointArrHash);

        return $difference;
    }

    /**
     * Находим наибольшую общую подпоследовательность
     *
     * Находим, на основе двух переданных массивов, наибольшую общую подпоследовательность.
     * https://ru.wikipedia.org/wiki/Наибольшая_общая_подпоследовательность
     * Сложность задачи O(n*m)
     *
     * @param $oArrHash
     * @param $nArrHash
     *
     * @return array
     */
    private static function LCSAlgorithm($oArrHash, $nArrHash)
    {
        $maxLen = [];
        
        for ($i=0, $x=count($oArrHash); $i<=$x; $i++)
        {
            $maxLen[$i] = [];
            for ($j=0, $y=count($nArrHash); $j<=$y; $j++)
            {
                $maxLen[$i][$j] = '';
            }
        }
        
        for ($i=count($oArrHash)-1; $i>=0; $i--)
        {
            for ($j=count($nArrHash)-1; $j>=0; $j--)
            {
                if ($oArrHash[$i] == $nArrHash[$j])
                {
                    $maxLen[$i][$j] = 1 + $maxLen[$i+1][$j+1];
                }
                else
                {
                    $maxLen[$i][$j] = max($maxLen[$i+1][$j],$maxLen[$i][$j+1]);
                }
            }
        }
        
        $jointArrHash = [];
        
        for ($i=0, $j=0; $maxLen[$i][$j]!=0 && $i<$x && $j<$y;)
        {
            if ($oArrHash[$i] == $nArrHash[$j])
            {
                $jointArrHash[] = $oArrHash[$i];
                $i++;
                $j++;
            }
            else
            {
                if ($maxLen[$i][$j] == $maxLen[$i+1][$j])
                {
                    $i++;
                }
                else
                {
                    $j++;
                }
            }
        }
        return $jointArrHash;
    }

    /**
     * Сравниваем два массива
     *
     * На входе у нас три массива: оригинальный, измененный, и общей подпоследовательности
     *
     * Сравниваем два массива формируя выходной массив
     * На выходе получаем массив из элементов, каждый элемент массива  - это массив из двух элементов
     * первый элемент строка из оригинального массива, второй - стока из измененного массива
     *
     * @param $oArrHash
     * @param $nArrHash
     * @param $jointArrHash
     *
     * @return array
     */
    private static function difference($oArrHash, $nArrHash, $jointArrHash)
    {
        $difference = [];

        for ($o=0, $n=0, $j=0; $o<=count($oArrHash)-1 || $n<=count($nArrHash)-1 || $j<=count($jointArrHash)-1;)
        {
            $oVar = isset($oArrHash[$o]) ? $oArrHash[$o] : '';
            $nVar = isset($nArrHash[$n]) ? $nArrHash[$n] : '';
            $jVar = isset($jointArrHash[$j]) ? $jointArrHash[$j] : '';

            if ($oVar == $jVar && $nVar == $jVar)
            {
                $difference[] = [
                    $oVar,
                    $nVar,
                ];
                $o++;
                $n++;
                $j++;
            }
            elseif ($oVar != $jVar && $nVar != $jVar)
            {
                $difference[] = [
                    $oVar,
                    $nVar,
                ];
                $o++;
                $n++;
            }
            elseif ($oVar == $jVar && $nVar != $jVar)
            {
                $difference[] = [
                    '',
                    $nVar,
                ];
                $n++;
            }
            elseif ($oVar != $jVar && $nVar == $jVar)
            {
                $difference[] = [
                    $oVar,
                    '',
                ];
                $o++;
            }
        }

        return $difference;
    }

    /**
     * Визуализируем различия в текстах
     *
     * Визуализируем различия второго текста, относительно первого.
     * Проходимся по массиву переданному в первом параметре, и раскодируем хэши обратно в строки.
     * Причем, каждый элемент массива, это массив из двух элементов, ведущим из которых является второй.
     * Визуализируем вывод так:
     * 1) если первый массив имеет данные, а второй нет то, - помечаем в выводе строку как удаленные
     * 2) если первый массив не имеет данных, а второй имеет то, - помечаем в выводе строку как добавленную
     * 3) если и в первом и во втором массиве есть данные то, - значение из второго массива выводим на страницу,
     *    а значение из первого массива выводим в скрытые данные строки
     *
     * @param $diffArrHash
     * @param $hashStrArr
     *
     * @return string
     */
    private static function visualizer($diffArrHash, $hashStrArr)
    {
        $vizualText = '';

        foreach ($diffArrHash as $item)
        {
            list($oItem, $nItem) = $item;

            if ($oItem && ! $nItem)
            {
                $vizualText .= '<p class="del">'.$hashStrArr[$oItem].'</p>';
            }
            elseif ( ! $oItem && $nItem)
            {
                $vizualText .= '<p class="ins">'.$hashStrArr[$nItem].'</p>';
            }
            elseif ($oItem && $nItem)
            {
                if ($oItem == $nItem)
                {
                    $vizualText .= '<p class="no">'.$hashStrArr[$nItem].'</p>';
                }
                else
                {
                    $vizualText .= '<p class="mod" data-storage="'.$hashStrArr[$oItem].'">'.$hashStrArr[$nItem].'</p>';
                }
            }
        }

        return $vizualText;
    }

}