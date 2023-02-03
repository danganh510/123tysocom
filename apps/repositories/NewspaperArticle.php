<?php

namespace Score\Repositories;

use Score\Models\ScNewspaperArticle;
use Phalcon\Mvc\User\Component;

class NewspaperArticle extends Component
{
    public static  function findFirstById($id){
        return ScNewspaperArticle::findFirst(array (
                "article_id = :ID:",
                'bind' => array('ID' => $id))
        );
    }
    public static function findFirstByNewspaperArticle($articleId) {
        return ScNewspaperArticle::findFirst([
            'article_newspaper_id = :ID:',
            'bind' => ['ID'=> $articleId]
        ]);
    }
    public function getByNewspaperIdAndInsertTime ($id, $lang, $limit = null){
        $result = array();
        $para = array('ID'=>$id);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Score\Models\ScNewspaperArticle a 
                    INNER JOIN \Score\Models\ScNewspaperArticleLang al 
                        ON a.article_id = al.article_id AND al.article_lang_code = :LANG: 
                    WHERE a.article_active = 'Y' AND a.article_newspaper_id = :ID: 
                    ORDER BY a.article_insert_time DESC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if(sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new ScNewspaperArticle(),array_merge($item->a->toArray(), $item->al->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Score\Models\ScNewspaperArticle 
                WHERE article_active = 'Y' AND article_newspaper_id = :ID: 
                ORDER BY article_insert_time DESC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
}