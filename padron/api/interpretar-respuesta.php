<?php
declare(strict_types=1);
function padronTexto(?string $text): string {return trim(preg_replace('/\s+/u',' ',$text ?? '') ?? '');}
function padronEtiqueta(string $text): string {return strtr(mb_strtolower(padronTexto($text),'UTF-8'),['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u']);}
function padronCampo(string $text): ?string {
 $text=trim(padronEtiqueta($text)," :¿?\t\n\r");
 if(mb_strlen($text)>100)return null;
 if(str_contains($text,'elector') || in_array($text,['elector','nombre del elector','apellido y nombre','apellidos y nombres','nombre y apellido','nombre y apellidos'],true))return 'apellidoNombre';
 if(str_contains($text,'colegio') || in_array($text,['colegio','colegio de abogados','colegio de la abogacia'],true))return 'colegio';
 if(str_contains($text,'donde vot') || preg_match('/^donde vot(?:a|as|ar)?[?]?$/D',$text) || in_array($text,['lugar de votacion','direccion de votacion','domicilio de votacion'],true))return 'direccionVotacion';
 return null;
}
function interpretarPadron(string $html): array {
 $old=libxml_use_internal_errors(true);$dom=new DOMDocument();
 $loaded=$dom->loadHTML('<?xml encoding="UTF-8">'.$html,LIBXML_NOERROR|LIBXML_NOWARNING);
 libxml_clear_errors();libxml_use_internal_errors($old);
 if(!$loaded)return ['status'=>'unexpected'];
 $xpath=new DOMXPath($dom);$root=$xpath->query('//*[@id="padron"]')->item(0);
 if(!$root)return ['status'=>'unexpected'];
 $text=padronEtiqueta($root->textContent);
 if(str_contains($text,'tus datos no se encuentran en el padron') || str_contains($text,'no se encontraron resultados') || str_contains($text,'no se encontraron datos'))return ['status'=>'not_found'];
 $result=['apellidoNombre'=>'','colegio'=>'','direccionVotacion'=>''];
 foreach($xpath->query('.//h2|.//h3|.//h4|.//h5|.//dt|.//th|.//strong|.//b|.//label',$root) as $label){
  $field=padronCampo($label->textContent);if(!$field)continue;
  $value='';
  // Traditional h4/h5, definition lists and table cells.
  for($node=$label->nextSibling;$node;$node=$node->nextSibling){
   if($node instanceof DOMElement && in_array(strtolower($node->tagName),['script','style','input','select','button','form'],true))break;
   $candidate=padronTexto($node->textContent);
   if($candidate==='')continue;
   if($node instanceof DOMElement && !in_array(strtolower($node->tagName),['h5','td','dd'],true) && padronCampo($candidate)!==null)break;
   $value=$candidate;break;
  }
  if($value!=='')$result[$field]=$value;
 }
 foreach($result as $value)if($value==='')return ['status'=>'unexpected'];
 return ['status'=>'found','result'=>$result];
}
