<?php

/**
 * Created by PhpStorm.
 * User: clovis
 * Date: 20/01/17
 * Time: 19:29
 */
class Template
{

    /**
     * Lit un fichier de template $file et retourne son contenu
     * @param string $file fichier de template à lire
     * @return string Contenu du fichier
     */
    public static function open($file)
    {
        $content = file_get_contents("Templates/".$file);
        return $content;
    }

    /**
     * Lit le contenu d'un template et produit du code html en fonction de $data
     * @param string $content contenu du template de base
     * @param array $data Dictionnaire de données à insérer dans le template
     * @param bool $fatal si vrai, arrete lors de la rencontre d'une erreur fatale
     * @return mixed contenu du template modifié
     * @throws Exception
     */
    public static function prepare($content, $data, $fatal = true)
    {

        $matches = array();

        // recherche et concaténation des includes
        preg_match_all("/<@(.*?)>/", $content, $matches);
        foreach($matches[1] as $match)
        {
            $key = str_replace(" ", "", $match).".html";
            $inner_content = Template::open($key);
            $inner_content = Template::prepare($inner_content, $data);
            $content = preg_replace("/(<@".$match.">)/", $inner_content, $content, 1);
        }

        // recherche et expansion des boucles

        $reg = "/<#(.*?)>\s*?(.*?)\s*?<\/#(.*?)>/";
        preg_match_all($reg, $content, $matches);
        for($i = 0; $i!= count($matches[1]); $i++)
        {
            $array_name = str_replace(" ", "", $matches[1][$i]);

            if(isset($data[$array_name]) == false)
            {
                throw new Exception($array_name." not specified in data.");
            }
            if(is_array($data[$array_name]) == false)
                continue;
            $body = "";

            for($u = 0; $u != count($data[$array_name]); $u++)
            {
                $body = $body.$matches[2][$i];
                $body = Template::prepare($body, $data[$array_name][$u], false);
            }
            $content = preg_replace("/<#(.*?)>\s*?(.*?)\s*?<\/#(.*?)>/", $body, $content, 1);

        }

        // recherche et remplacement des données
        //recherche des variables
        preg_match_all("/<=(.*?)>/", $content, $matches);
        foreach($matches[1] as $match)
        {
            $key = str_replace(" ", "", $match);
            if(isset($data[$key]) == false && $fatal == true)
            {
                throw new Exception($key." not specified in data.");
            }
            else if(isset($data[$key]) == false && $fatal == false)
                continue;
            $content = preg_replace("/<=".$match.">/", $data[$key], $content, 1);
        }

        return $content;
    }
}