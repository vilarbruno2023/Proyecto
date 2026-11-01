<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if (empty($_SESSION['idUsuario'])) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'Iniciá sesión.']); exit; }
$rol = strtolower((string)($_SESSION['rol'] ?? ''));
$accion = (string)($_GET['accion'] ?? $_POST['accion'] ?? 'estado');
$soloAdmin = ['usuarios','encuestas','flota','crear_usuario','cambiar_clave','crear_personal','crear_vehiculo','estado_personal','estado_vehiculo','crear_categoria'];
$rolesIndicaciones = ['admin','doctor','documentacion'];
if (in_array($accion,$soloAdmin,true) && $rol!=='admin') { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'Acceso denegado.']); exit; }
if ($accion==='indicaciones' && !in_array($rol,$rolesIndicaciones,true)) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'Acceso denegado.']); exit; }
if ($accion==='crear_indicacion' && $rol!=='documentacion') { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'Solo Documentación puede crear indicaciones.']); exit; }
if ($accion==='aprobar_indicacion' && $rol!=='doctor') { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'Solo un doctor puede revisar indicaciones.']); exit; }
require_once __DIR__.'/../Datos/conexion.php';
function out($data){ echo json_encode(['ok'=>true]+$data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function postOnly(){ if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);throw new RuntimeException('Método no permitido.');}}
try {
 switch($accion) {
 case 'estado': out(['usuario'=>$_SESSION['nombre']??'','rol'=>$rol]);
 case 'indicaciones':
   $cats=$conexion->query('SELECT idCategoria,nombreCategoria FROM categoria ORDER BY nombreCategoria')->fetchAll(PDO::FETCH_ASSOC);
   $rows=$conexion->query('SELECT d.idDocumento,d.titulo,d.resumen,d.archivo,d.estado,d.idCategoria,c.nombreCategoria,f.nombreUsuario AS creador FROM documento d JOIN categoria c ON c.idCategoria=d.idCategoria JOIN funcionario f ON f.idUsuario=d.idUsuario ORDER BY d.idDocumento DESC')->fetchAll(PDO::FETCH_ASSOC);
   out(['categorias'=>$cats,'indicaciones'=>$rows,'puedeCrear'=>$rol==='documentacion','puedeAprobar'=>$rol==='doctor','puedeCategorias'=>$rol==='admin']);
 case 'usuarios':
   out(['usuarios'=>$conexion->query('SELECT idUsuario,nombreUsuario,rol FROM funcionario ORDER BY nombreUsuario')->fetchAll(PDO::FETCH_ASSOC)]);
 case 'encuestas':
   out(['respuestas'=>$conexion->query('SELECT fechaEncuesta,tipoEncuesta,segmento,satisfaccion FROM encuesta_respuesta ORDER BY fechaEncuesta DESC')->fetchAll(PDO::FETCH_ASSOC)]);
 case 'flota':
   out(['personal'=>$conexion->query('SELECT * FROM personal_traslado ORDER BY funcion,nombre')->fetchAll(PDO::FETCH_ASSOC),'vehiculos'=>$conexion->query('SELECT * FROM vehiculo ORDER BY tipo,codigo')->fetchAll(PDO::FETCH_ASSOC)]);
 case 'crear_usuario':
   postOnly(); $u=trim((string)($_POST['nombreUsuario']??'')); $pw=(string)($_POST['contrasenia']??''); $r=(string)($_POST['rol']??'');
   if(!preg_match('/^[a-zA-Z0-9._-]{3,50}$/',$u)||strlen($pw)<10||!in_array($r,['funcionario','documentacion','doctor','operador','admin'],true)) throw new InvalidArgumentException('Revisá usuario, contraseña (10 caracteres mínimo) y rol.');
   $q=$conexion->prepare('INSERT INTO funcionario(nombreUsuario,contrasenia,rol) VALUES(?,?,?)'); $q->execute([$u,password_hash($pw,PASSWORD_DEFAULT),$r]); out(['mensaje'=>'Usuario creado.']);
 case 'cambiar_clave':
   postOnly(); $id=filter_var($_POST['idUsuario']??null,FILTER_VALIDATE_INT); $pw=(string)($_POST['nuevaContrasenia']??''); if(!$id||strlen($pw)<10) throw new InvalidArgumentException('Contraseña inválida.');
   $q=$conexion->prepare('UPDATE funcionario SET contrasenia=? WHERE idUsuario=?'); $q->execute([password_hash($pw,PASSWORD_DEFAULT),$id]); out(['mensaje'=>'Contraseña actualizada.']);
 case 'crear_personal':
   postOnly(); $nom=trim((string)($_POST['nombre']??'')); $doc=trim((string)($_POST['documento']??'')); $fun=(string)($_POST['funcion']??'');
   if(!$nom||!in_array($fun,['doctor','chofer','enfermero','administrativo'],true)) throw new InvalidArgumentException('Datos de personal inválidos.');
   $q=$conexion->prepare('INSERT INTO personal_traslado(nombre,documento,funcion) VALUES(?,?,?)');$q->execute([$nom,$doc?:null,$fun]);out(['mensaje'=>'Personal registrado.']);
 case 'crear_vehiculo':
   postOnly();$cod=trim((string)($_POST['codigo']??''));$tipo=(string)($_POST['tipo']??'');$cap=filter_var($_POST['capacidad']??null,FILTER_VALIDATE_INT);
   if(!$cod||!in_array($tipo,['ambulancia','auto','utilitario','otro'],true)||!$cap||$cap<1||$cap>50)throw new InvalidArgumentException('Datos de vehículo inválidos.');
   $q=$conexion->prepare('INSERT INTO vehiculo(codigo,tipo,descripcion,capacidad,requiere_asistencia) VALUES(?,?,?,?,?)');$q->execute([$cod,$tipo,trim((string)($_POST['descripcion']??''))?:null,$cap,isset($_POST['requiere_asistencia'])?1:0]);out(['mensaje'=>'Vehículo registrado.']);
 case 'estado_personal': case 'estado_vehiculo':
   postOnly();$id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);$activo=(int)($_POST['activo']??-1);if(!$id||!in_array($activo,[0,1],true))throw new InvalidArgumentException('Datos inválidos.');
   if($accion==='estado_personal'){$q=$conexion->prepare('UPDATE personal_traslado SET activo=? WHERE idPersonal=?');}else{$q=$conexion->prepare('UPDATE vehiculo SET activo=? WHERE idVehiculo=?');}$q->execute([$activo,$id]);out(['mensaje'=>'Estado actualizado.']);
 case 'crear_categoria':
   postOnly();$nom=trim((string)($_POST['nombreCategoria']??''));if(!$nom||mb_strlen($nom)>50)throw new InvalidArgumentException('Nombre de categoría inválido.');
   $q=$conexion->prepare('INSERT INTO categoria(nombreCategoria) VALUES(?)');$q->execute([$nom]);out(['mensaje'=>'Categoría agregada.']);
 case 'aprobar_indicacion':
   postOnly();$id=filter_var($_POST['idDocumento']??null,FILTER_VALIDATE_INT);$estado=(string)($_POST['estado']??'');if(!$id||!in_array($estado,['Revisar','Vigente','Rechazada'],true))throw new InvalidArgumentException('Datos inválidos.');
   $q=$conexion->prepare('UPDATE documento SET estado=? WHERE idDocumento=?');$q->execute([$estado,$id]);out(['mensaje'=>'Estado actualizado.']);
 default: http_response_code(400);throw new InvalidArgumentException('Acción desconocida.');
 }
} catch(InvalidArgumentException $e){http_response_code(400);echo json_encode(['ok'=>false,'error'=>$e->getMessage()],JSON_UNESCAPED_UNICODE);}
catch(PDOException $e){error_log('Administración SIGSM: '.$e->getMessage());http_response_code(409);echo json_encode(['ok'=>false,'error'=>'No se pudo guardar. Verificá que el dato no esté duplicado y que la base esté actualizada.'],JSON_UNESCAPED_UNICODE);}
catch(Throwable $e){error_log('Administración SIGSM: '.$e->getMessage());http_response_code(500);echo json_encode(['ok'=>false,'error'=>'Error interno.'],JSON_UNESCAPED_UNICODE);}
