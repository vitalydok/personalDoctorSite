<?php
define('MODX_API_MODE', true);
require $_SERVER['DOCUMENT_ROOT'] . '/index.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modx();
$modx->initialize('web');

$idSession = $_POST['resourceID'];


error_reporting(E_ALL);
ini_set('display_errors', 'On');

if (empty($_SESSION['resource_id'])){
    $_SESSION['resource_id'] = $idSession;
    if (!empty($_POST['resourceID']) && !empty($_POST['starRatingValue']))
        {
            $resource_id = $_POST['resourceID']; // id ресурса на котором рейтинг тыкнут
            $rating_value = $_POST['starRatingValue']; // оценка (1,2,3,4,5)
            $rating_value = (int)$rating_value;

            $resource_for_work = $modx->getObject('modResource', $resource_id); // получил ресурс
            $rating_resource = $resource_for_work->getTVValue('article__rating'); // получил migx
            $articleLikes = $resource_for_work->getTVValue('likes');

            $articleLikes = json_decode($articleLikes, true);
            $array_for_change = json_decode($rating_resource, true); // массив для изменений


            if (empty($array_for_change))
            {
                if ($rating_value == 4 || $rating_value == 5)
                {
                    $array_for_change[0]['rating__like'] += 1;
                }
                else
                {
                    $array_for_change[0]['rating__like'] += 0;
                }
                if ($rating_value == 1)
                {
                    $array_for_change[0]['rating__1'] = (int)$array_for_change[0]['rating__1'];
                    $array_for_change[0]['rating__1'] += 1;
                    $array_for_change[0]['rating__1'] = (string)$array_for_change[0]['rating__1'];
                    $array_for_change[0]['rating__2'] = 0;
                    $array_for_change[0]['rating__3'] = 0;
                    $array_for_change[0]['rating__4'] = 0;
                    $array_for_change[0]['rating__5'] = 0;
                }
                else if ($rating_value == 2)
                {
                    $array_for_change[0]['rating__2'] = (int)$array_for_change[0]['rating__2'];
                    $array_for_change[0]['rating__2'] += 2;
                    $array_for_change[0]['rating__2'] = (string)$array_for_change[0]['rating__2'];
                    $array_for_change[0]['rating__1'] = 0;
                    $array_for_change[0]['rating__3'] = 0;
                    $array_for_change[0]['rating__4'] = 0;
                    $array_for_change[0]['rating__5'] = 0;
                }
                else if ($rating_value == 3)
                {
                    $array_for_change[0]['rating__3'] = (int)$array_for_change[0]['rating__3'];
                    $array_for_change[0]['rating__3'] += 3;
                    $array_for_change[0]['rating__3'] = (string)$array_for_change[0]['rating__3'];
                    $array_for_change[0]['rating__2'] = 0;
                    $array_for_change[0]['rating__1'] = 0;
                    $array_for_change[0]['rating__4'] = 0;
                    $array_for_change[0]['rating__5'] = 0;
                }
                else if ($rating_value == 4)
                {
                    $array_for_change[0]['rating__4'] = (int)$array_for_change[0]['rating__4'];
                    $array_for_change[0]['rating__4'] += 4;
                    $array_for_change[0]['rating__4'] = (string)$array_for_change[0]['rating__4'];
                    $array_for_change[0]['rating__2'] = 0;
                    $array_for_change[0]['rating__3'] = 0;
                    $array_for_change[0]['rating__1'] = 0;
                    $array_for_change[0]['rating__5'] = 0;
                }
                else if ($rating_value == 5)
                {
                    $array_for_change[0]['rating__5'] = (int)$array_for_change[0]['rating__5'];
                    $array_for_change[0]['rating__5'] += 5;
                    $array_for_change[0]['rating__5'] = (string)$array_for_change[0]['rating__5'];
                    $array_for_change[0]['rating__2'] = 0;
                    $array_for_change[0]['rating__3'] = 0;
                    $array_for_change[0]['rating__4'] = 0;
                    $array_for_change[0]['rating__1'] = 0;
                }
                $array_for_change[0]['rating__all_count'] += 1;
                $new_arr = array(
                    'MIGX_id' => 0,
                    'rating__1' => $array_for_change[0]['rating__1'],
                    'rating__2' => $array_for_change[0]['rating__2'],
                    'rating__3' => $array_for_change[0]['rating__3'],
                    'rating__4' => $array_for_change[0]['rating__4'],
                    'rating__5' => $array_for_change[0]['rating__5'],
                    'rating__all_count' => $array_for_change[0]['rating__all_count'],
                    'rating__like' => $array_for_change[0]['rating__like']
                );
            }
            else
            {
                $rating_field = "rating__" . $rating_value;
                $array_for_change[0][$rating_field] += $rating_value;

                if ($rating_value == 4 || $rating_value == 5)
                {
                    $array_for_change[0]['rating__like'] = (int)$array_for_change[0]['rating__like'];
                    $array_for_change[0]['rating__like']++;
                    $array_for_change[0]['rating__like'] = (string)$array_for_change[0]['rating__like'];
                }

                $array_for_change[0]['rating__all_count'] = (int)$array_for_change[0]['rating__all_count'];
                $array_for_change[0]['rating__all_count']++;
                $array_for_change[0]['rating__all_count'] = (string)$array_for_change[0]['rating__all_count'];

            };

            $average_rating = ((int)$array_for_change[0]['rating__1'] + (int)$array_for_change[0]['rating__2'] + (int)$array_for_change[0]['rating__3'] + (int)$array_for_change[0]['rating__4'] + (int)$array_for_change[0]['rating__5']) / $array_for_change[0]['rating__all_count'];
            $average_rating = round($average_rating, 2);

            $count_rating = $array_for_change[0]['rating__all_count'];

            $array_out = array(
                $average_rating,
                $count_rating
            );

            $incomeValues = array(
                'average_rating' => $average_rating,
                'count_rating' => $count_rating,
                'error_rating' => ''
            );
            echo json_encode($incomeValues);

            $resource_for_work->setTVValue('article__rating', json_encode($array_for_change));
            $resource_for_work->setTVValue('likes', $array_for_change[0]['rating__like']);
            $resource_for_work->save();
            $cacheKey = $resource_for_work->getCacheKey();
            $modx->cacheManager->refresh();
            return true;
        }



} else {

     $incomeValues = array(
                    'average_rating' => 'а тут вроде пусто',
                    'count_rating' => 'и тут тоже',
                    'error_rating' => 'Вы уже голосовали!'
                );
                echo json_encode($incomeValues);
}


