<?php

defined('ABSPATH') or exit;

class BangumiAPI
{
    private $apiUrl = 'https://api.bgm.tv';
    private $userID;
    private $collectionApi = '';

    public function __construct($_userID, $_apiUrl = null)
    {
        if (empty($_userID)) return false;
        if (!empty($_apiUrl)) $this->apiUrl = $_apiUrl;

        $this->userID = $_userID;
        $this->collectionApi = $this->apiUrl . '/v0/users/' . $this->userID . '/collections';
    }

    public function getCollections($isWatching, $isWatched)
    {
        //return $this->http_get_contents($this->collectionApi);
        $collOffset = 0;
        $collDataArr = [];

        if ($isWatching) {
            do {
                $collData = json_decode($this->http_get_contents($this->collectionApi . '?subject_type=2&type=3&limit=50&offset=' . $collOffset), true);
                $collDataArr = array_merge($collDataArr, $collData['data']);
                $collOffset += 50;
            } while ($collOffset < $collData['total']);
        }

        if ($isWatched) {
            $collOffset = 0;
            do {
                $collData = json_decode($this->http_get_contents($this->collectionApi . '?subject_type=2&type=2&limit=50&offset=' . $collOffset), true);
                $collDataArr = array_merge($collDataArr, $collData['data']);
                $collOffset += 50;
            } while ($collOffset < $collData['total']);
        }

        $collArr = [];
        foreach ($collDataArr as $value) {
            $collArr[] = [
                'name' => $value['subject']['name'],
                'name_cn' => $value['subject']['name_cn'],
                'date' => $value['subject']['date'],
                'url' => 'https://bgm.tv/subject/' . $value['subject']['id'],
                'images' => $value['subject']['images']['grid'],
                'eps' => $value['subject']['eps'],
                'ep_status' => $value['ep_status'],
            ];
        }

        return $collArr;
    }

    private static function http_get_contents($_url)
    {
        $response = wp_remote_get($_url, ['user-agent' => 'fhyuncai/BangumiList/' . BGMLIST_VER . ' (WordPressPlugin) (https://github.com/fhyuncai/Bangumi-List)']);
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $body = wp_remote_retrieve_body($response);
        } else {
            $body = 'Error: ' . is_wp_error($response) ? $response->get_error_message() : 'An unknown error occurred.';
        }
        return $body;
    }
}

function getBangumiData()
{
    $BangumiOptions = get_option('bangumi_list');
    if (is_array($BangumiOptions) && $BangumiOptions['bangumiID']) {
        $userId = $BangumiOptions['bangumiID'];
        $isCache = (bool)$BangumiOptions['isCache'];
        $isProxy = (bool)$BangumiOptions['isProxy'];
        $isWatching = (bool)$BangumiOptions['isWatching'];
        $isWatched = (bool)$BangumiOptions['isWatched'];
        $proxyApi = ($isProxy ? 'http://api.bgm.atkoi.cn' : null);
        $mainColor = $BangumiOptions['color'];
        $singleItemNum = $BangumiOptions['singleItemNum'];
        $singleNavNum = $BangumiOptions['singleNavNum'];

        $bgm = new BangumiAPI($userId, $proxyApi);

        $bangumiResCode = 200;

        if ($isCache) {
            $cachePath = plugin_dir_path(__FILE__) . 'BangumiCache.json'; // 缓存文件路径
            $nowDate = date("Y-m-d");

            if (is_file($cachePath)) $cacheData = json_decode(file_get_contents($cachePath), true);

            if (is_file($cachePath) && $cacheData['date'] == $nowDate && $cacheData['user'] == $userId) {
                $content = $cacheData['data'];
            } else {
                $content = $bgm->getCollections($isWatching, $isWatched);
                file_put_contents($cachePath, json_encode(['user' => $userId, 'date' => $nowDate, 'data' => $content]));
            }
        } else {
            $content = $bgm->getCollections($isWatching, $isWatched);
        }
    } else {
        $bangumiResCode = 202;
        $content = '_(:3」」好像还没有填写 Bangumi ID 呢';
    }
    $bangumiRes = json_encode([
        'messageType' => 'bangumi_list_data',
        'messageCode' => $bangumiResCode,
        'messageContent' => [
            'singleItemNum' => $singleItemNum <= 0 ? 6 : $singleItemNum,
            'singleNavNum' => $singleNavNum <= 0 ? 3 : $singleNavNum,
            'mainColor' => empty($mainColor) ? '#ff8c83' : $mainColor,
            'content' => empty($content) ? [] : $content
        ]
    ]);
    echo $bangumiRes;
    die();
}

add_action("wp_ajax_nopriv_getBangumiData", "getBangumiData");
add_action("wp_ajax_getBangumiData", "getBangumiData");
