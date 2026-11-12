<?php
function ij_pagination_items(int $page,int $total): array {
    if($total<=7)return range(1,$total);
    $numbers=$page<=3?array_merge(range(1,4),[$total]):($page>=$total-2?array_merge([1],range($total-3,$total)):[1,$page-1,$page,$page+1,$total]);
    $items=[];$previous=0;foreach($numbers as $number){if($previous && $number-$previous>1)$items[]=null;$items[]=$number;$previous=$number;}return $items;
}
function ij_pagination_url(string $base,int $page): string {
    $path=parse_url($base,PHP_URL_PATH) ?: '/';$query=[];parse_str(parse_url($base,PHP_URL_QUERY) ?: '',$query);unset($query['pagina']);if($page>1)$query['pagina']=$page;return $path.($query?'?'.http_build_query($query,'','&',PHP_QUERY_RFC3986):'');
}
