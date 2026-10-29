<?php
declare(strict_types=1);
const CPACF_PADRON_URL='https://w3.cpacf.org.ar/busquedapadronconsejo/padronprovisorio.aspx';
function cpacfDom(string $html): DOMXPath {
    $old=libxml_use_internal_errors(true);$dom=new DOMDocument();$dom->loadHTML('<?xml encoding="UTF-8">'.$html,LIBXML_NOERROR|LIBXML_NOWARNING);libxml_clear_errors();libxml_use_internal_errors($old);return new DOMXPath($dom);
}
function cpacfText(string $value): string {return trim(preg_replace('/\s+/u',' ',$value) ?? '');}
function cpacfValue(string $label,string $value): string {
    $key=strtr(mb_strtolower($label),['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u']);
    // Some legacy records use a slashed o for the ordinal after a floor number.
    if(preg_match('/padron|direccion|domicilio|piso/',$key)) {
        $value=preg_replace('/(?<=[0-9])[øØ](?=\s*(?:[\p{L}"\x{27}]|$|[-,.;]))/u','º',$value) ?? $value;
    }
    return $value;
}
function cpacfForm(string $html): array {
    $xp=cpacfDom($html);$form=$xp->query('//form[@id="form1"]')->item(0);
    if(!$form || !$xp->query('.//input[@name="txtTomo"]',$form)->length || !$xp->query('.//input[@name="txtFolio"]',$form)->length)throw new RuntimeException('Formulario no reconocido');
    $fields=[];foreach($xp->query('.//input[@type="hidden"]',$form) as $input)$fields[$input->getAttribute('name')]=$input->getAttribute('value');
    if(empty($fields['__VIEWSTATE']) || empty($fields['__EVENTVALIDATION']))throw new RuntimeException('Formulario incompleto');
    $submit=$xp->query('.//input[@name="btnAceptar"]',$form)->item(0);if(!$submit)throw new RuntimeException('Formulario incompleto');
    $fields['btnAceptar']=$submit->getAttribute('value');return $fields;
}
function cpacfParse(string $html): array {
    $xp=cpacfDom($html);$count=$xp->query('//*[@id="lblCantidad"]')->item(0);
    if($count && str_contains(mb_strtolower(cpacfText($count->textContent)),'no se encontraron resultados'))return ['status'=>'not_found'];
    $grid=$xp->query('//table[@id="grdDatosMatriculados"]')->item(0);
    if(!$grid)return ['status'=>'unexpected'];
    $rows=$xp->query('./tr|./tbody/tr',$grid);if($rows->length!==2)return ['status'=>'unexpected'];
    $labels=$xp->query('./th|./td',$rows->item(0));$cells=$xp->query('./td',$rows->item(1));
    if($labels->length!==$cells->length || $cells->length<2)return ['status'=>'unexpected'];
    $fields=[];
    foreach($cells as $index=>$cell){$label=cpacfText($labels->item($index)->textContent);$value=cpacfText($cell->textContent);if($label!=='' && $value!=='')$fields[]=['label'=>$label,'value'=>cpacfValue($label,$value)];}
    if(count($fields)<2)return ['status'=>'unexpected'];
    return ['status'=>'found','fields'=>$fields];
}
function cpacfLookup(string $tomo,string $folio): array {
    $ch=curl_init(CPACF_PADRON_URL);
    try {
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>12,CURLOPT_ENCODING=>'',CURLOPT_COOKIEFILE=>'',CURLOPT_USERAGENT=>'InfoJuridica-ConsultaPadron/2.0',CURLOPT_HTTPHEADER=>['Accept: text/html,application/xhtml+xml']]);
        $html=curl_exec($ch);if($html===false || curl_getinfo($ch,CURLINFO_HTTP_CODE)!==200)throw new RuntimeException('Fuente no disponible');
        $fields=cpacfForm($html);$fields['txtTomo']=$tomo;$fields['txtFolio']=$folio;
        curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>http_build_query($fields),CURLOPT_HTTPHEADER=>['Content-Type: application/x-www-form-urlencoded','Accept: text/html,application/xhtml+xml','Referer: '.CPACF_PADRON_URL]]);
        $html=curl_exec($ch);if($html===false || curl_getinfo($ch,CURLINFO_HTTP_CODE)!==200)throw new RuntimeException('Fuente no disponible');
        return cpacfParse($html);
    } finally {unset($ch);}
}
