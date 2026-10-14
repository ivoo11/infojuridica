<?php
require_once __DIR__.'/bootstrap.php';
function ij_documents_ready(): void {static $ready=false;if(!$ready){ij_db()->exec(file_get_contents(__DIR__.'/../database/post-documents.sql'));$ready=true;}}
function ij_document(int $postId): ?array {if(!ij_live())return null;ij_documents_ready();$s=ij_db()->prepare('SELECT * FROM post_documents WHERE post_id=?');$s->execute([$postId]);return $s->fetch() ?: null;}
function ij_document_upload(string $field): ?array {
 $f=$_FILES[$field] ?? null;if(!$f || $f['error']===UPLOAD_ERR_NO_FILE)return null;
 if($f['error']!==UPLOAD_ERR_OK)throw new InvalidArgumentException('No se pudo subir el PDF. Revisá el límite de subida de PHP en el hosting.');
 if($f['size']<8 || $f['size']>10*1024*1024 || !is_uploaded_file($f['tmp_name']))throw new InvalidArgumentException('El PDF debe pesar hasta 10 MB.');
 if((new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name'])!=='application/pdf' || file_get_contents($f['tmp_name'],false,null,0,5)!=='%PDF-')throw new InvalidArgumentException('Adjuntá un archivo PDF válido.');
 $directory=__DIR__.'/../uploads/documents';
 if(!is_dir($directory) && !mkdir($directory,0755,true))throw new InvalidArgumentException('No se pudo crear la carpeta de documentos.');
 // Serve through documento.php only, including for drafts and scheduled articles.
 if(file_put_contents($directory.'/.htaccess',"Require all denied\n")===false)throw new InvalidArgumentException('No se pudo proteger la carpeta de documentos.');
 $name=bin2hex(random_bytes(16)).'.pdf';
 if(!move_uploaded_file($f['tmp_name'],$directory.'/'.$name))throw new InvalidArgumentException('No se pudo guardar el PDF. Revisá los permisos de uploads.');
 return ['filename'=>$name,'size_bytes'=>(int)$f['size']];
}
function ij_document_file(array $document): ?string {if(!preg_match('/^[a-f0-9]{32}\.pdf$/D',$document['filename']))return null;$path=__DIR__.'/../uploads/documents/'.$document['filename'];return is_file($path)?$path:null;}
