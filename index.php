<?php

$estados = json_decode(file_get_contents(__DIR__.'/cidades.json'), true);

$conteudoMascara = file_get_contents(__DIR__.'/index_para_indexCidades.html');

foreach($estados['estados'] as $estado){

    $uf = $estado['sigla'];
    $estadoNome = $estado['nome'];

    $cidades = $estado['cidades'];

    $urlsSiteMap = [];

    foreach($cidades as $cidade){

        $url = iconv('UTF-8', 'ASCII//TRANSLIT', mb_strtolower($cidade.'-'.$uf));
        $url = str_replace([
            ' ', '(', ')', 'ª', 'º', '°', '´', '`', '_', '-'
        ],'-', $url);

        $title = 'Dr. Eduardo Vanin Rodrigues - Advogado Criminalista em '.$cidade.' - '.mb_strtoupper($uf);
        
        $mustache = [
            '{{title}}' => $title,
            '{{cidade}}' => $cidade,
            '{{uf}}' => mb_strtoupper($uf),
            '{{url_link}}' => $url,
        ];

        $conteudo = str_replace(array_keys($mustache), array_values($mustache), $conteudoMascara);

        if(($argv[1] ?? '') == 'deletar'){
            unlink(__DIR__.'/src/'.$url.'.html');
            continue;
        }

        $urlsSiteMap[] = '
<url>
    <loc>https://eduardovrodrigues.adv.br/'.$url.'</loc>
    <lastmod>'.date('Y-m-d').'T01:48:30+00:00</lastmod>
    <priority>0.80</priority>
</url>';

        file_put_contents(__DIR__.'/src/'.$url.'.html', $conteudo);
    }
}

$sitemal = file_get_contents(__DIR__.'/src/sitemap.xml');
$sitemal = str_replace('{{urls}}', implode('', $urlsSiteMap), $sitemal);

file_put_contents(__DIR__.'/src/sitemap.xml', $sitemal);