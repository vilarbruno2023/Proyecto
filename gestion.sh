#!/bin/bash

source ./gestion_usuarios.sh
source ./gestion_grupos.sh
source ./gestion_respaldos.sh
source ./gestion_firewall.sh
source ./logs.sh

opc=9;

while [[ $opc -ne 6 ]]; do

    clear

    echo "---Seleccione una opcion---"
    echo "1. Gestionar usuarios"
    echo "2. Gestionar grupos"
    echo "3. Gestionar respaldos"
    echo "4. Gestionar firewall"
    echo "5. Gestionar logs"
    echo "6. Salir"
    echo "---------------------------"

    read -p "Opción: " opc

    case $opc in
        1) usuarios;;
        2) grupos;;
        3) respaldos;;
        4) firewall;;
        5) logs;;
        6) ;;
        *) echo "Seleccióne una opción valida";;
    esac

done

#!/bin/bash
# journalctl -t sudo <- muestra solo los "sudo" por tanto se usa para filtrar
# journalctl -t sudo | grep 'COMMAND=' <- el command 

inicioSes() {
    clear
    last | awk '{ if ($1 != "reboot" && $1 != "Debian-g") print }'
}

superUsu(){
    clear
    sudo journalctl -t sudo | grep 'COMMAND='
}

usoSSH(){
    read -p "Uso de ssh en el incio n°: " n

    journalctl -b -$n _COMM=sshd
}


logs() {
    opc=7

    while [[ $opc -ne 4 ]]; do

    echo "---Seleccióne una opción---"
    echo "1. Observar inicios de Sesión"
    echo "2. Observar uso de super usuario"
    echo "3. Conexión por SSH"
    echo "4. Salir"
    echo "---------------------------"

    read -p "Opción: " opc

        case $opc in
        1) inicioSes;;
        2) superUsu;;
        3) usoSSH;;
        4);;
        *) echo "Seleccióne una opción valida";;
        esac
    done
}


#!/bin/bash

#dev/null desaparece los archivos (dentro de null)
# -z Comprueba si la variable está vacia
# -m Crea el directorio si no existe
# -s Establece el shell que utiliza el usuario
#IFS=: Separador de campos a través de :

usuarioExiste() {
    getent passwd $1 > /dev/null
}



crearUsuario() {
    clear
    local usu
    read -p "Nombre del nuevo usuario: " usu

    if [[ -z "$usu" ]]; then
        echo "El nombre no puede estar vacío"

    elif usuarioExiste "$usu"; then
        echo "El usuario ya existe"

    else
        /usr/sbin/useradd -d "/home/$usu" -m -s /bin/bash "$usu"

        if [[ $? -eq 0 ]]; then
            echo "El usuario se ha creado correctamente"
            passwd "$usu"
        else
            echo "Ha ocurrido un problema, no se ha podido crear el usuario"
        fi
    fi
}


eliminarUsuario() {
    clear
    local usu
    read -rp "Nombre del usuario que se desea eliminar: " usu

    if [[ -z $usu ]]; then
        echo "Por favor seleccione el nombre de usuario que desee eliminar"

    elif ! usuarioExiste $usu; then
        echo "El usuario no existe"
    else 
        echo "Desea eliminar los archivos del usuario?"
        echo "1. Si"
        echo "2. No"
        read -p "Opción: " elec

        if [[ $elec -eq 1 ]]; then
            userdel -r $usu

            if [[ $? -eq 0 ]]; then
                echo "El usuario se ha eliminado correctamente"
            else
                echo "Ha ocurrido un problema, no se ha podido eliminado el usuario"
            fi

        elif [[ $elec -eq 2 ]]; then
            userdel $usu

            if [[ $? -eq 0 ]]; then
                echo "El usuario se ha eliminado correctamente"
            else
                echo "Ha ocurrido un problema, no se ha podido eliminar el usuario"
            fi
        else
            echo "Por favor seleccione una de las opciones establecidas"
        fi

    fi
}


cambiarContra() {
    clear
    local usu
    read -rp "Ingrese el usuario al que dese cambiar su contraseña: " usu

    if usuarioExiste $usu; then
        passwd $usu
    else
        echo "El usuario selecionado no existe"
    fi
}


verUsuarios() {
    clear
    local usu pass uid guid com hom shell

    echo "Usuarios que pueden iniciar sesión en el sistema: "
    echo

    while IFS=: read -r usu pass uid guid com hom shell; do
        if [[ $uid -ge 1000 && $usu != "nobody" ]]; then
            echo "Usuario: $usu"
        fi

    done < /etc/passwd
}



usuarios() {

clear
local opc=7

while [[ $opc -ne 5 ]]; do

    echo "---Seleccione una opcion---"
    echo "1. Agregar usuario"
    echo "2. Eliminar usuario"
    echo "3. Cambiar contraseña"
    echo "4. Listar los usuarios existentes"
    echo "5. Salir"
    echo "---------------------------"

    read -p "opción: " opc

    case $opc in
        1) crearUsuario;;
        2) eliminarUsuario;;
        3) cambiarContra;;
        4) verUsuarios;;
        *) echo "Seleccióne una opción valida";;
    esac
done
}


#!/bin/bash
# crontab -l lista todas las tareas que el usuario ya ha programado
# crontab -l 2>/dev/null Salida de errores, manda los errores de crontab a la carpeta null (no se muestra)
# comando="tar -czf $destino/$fichero.tar.gz $ruta" Se utiliza para poder lograr cambiar a futuro el comando, es decir, poder manipular los valores, por eso no se utilizaria un comando=$() porque seria utilizar lo que sale del copmando
# (Apoyo de IA para lograr manipular chron)


crearRespaldoL() {
    DESTINO="/home/srv/scripts/respaldos"
    mkdir -p "$DESTINO"

    local origen nombre archivo

    read -rp "Ruta a respaldar: " origen

    if [[ ! -e "$origen" ]]; then
        echo "Error, el archivo no existe"
        return
    fi

    nombre=$(basename "$origen")
    archivo="$DESTINO/$nombre-$(date +%Y%m%d_%H%M%S).tar.gz"

    tar -cvzf "$archivo" -C "$(dirname "$origen")" "$nombre"

}


crearRespaldoR(){
    read -p "Ruta local donde guardar el respaldo: " origen
    read -p "Ruta del fichero a respaldar: " ruta
    read -p "Usuario del dispositivo remoto: " usuario
    read -p "IP del dispositivo en el cual respaldar: " ip
    read -p "Destino del respaldo remoto: " destino

    if [[ ! -e $origen ]];then
        echo "Error, la carpeta que quieres comprimir, no existe"
        return
    fi

    if [[ ! -e "$ruta" ]]; then
        echo "Error: la ruta que quieres respaldar no existe"
        return
    fi

    nombre=$(basename "$origen")
    archivo="$DESTINO/$nombre-$(date +%Y%m%d_%H%M%S).tar.gz"

    tar -cvzf "$archivo" -C "$(dirname "$origen")" "$nombre"

    scp "$archivo" "$usuario@$ip:$destino"
}


descRespaldo() {
    read -p "Nombre del fichero a descomprimir (con .tar.gz): " ficherodesc
    read -p "Ruta del fichero a descomprimir: " ruta
    read -p "Ruta del destino: " destino


    tar -xvzf "$ruta/$ficherodesc" -C "$destino"
}


programarRespaldo() {
    read -p "Minuto (0-59): " minuto
    read -p "Hora (0-23): " hora
    read -p "Día del mes (1-31): " dia
    read -p "Mes (1-12): " mes
    read -p "Día de la semana (0-7): " semana

    read -p "Ruta del fichero a respaldar: " ruta
    read -p "Nombre del respaldo: " fichero
    read -p "Ruta donde guardar el respaldo: " destino

    mkdir -p "$destino"

    comando="tar -czf '$destino/$fichero.tar.gz' '$ruta'"

    (crontab -l 2>/dev/null; echo "$minuto $hora $dia $mes $semana $comando") | crontab - 
}


respaldos() {
    opc=6

    while [[ $opc -ne 5 ]]; do
        echo "--------------------------------"
        echo "Elija una opción:"
        echo "1. Crear un archivo de respaldo Local"
        echo "2. Crear un archivo de respaldo Remoto"
        echo "3. Descomprimir el Respaldo"
        echo "4. Programar un respaldo"
        echo "5. Salir"
        echo "--------------------------------"

        read -p "Opción: " opc

        case $opc in
            1) crearRespaldoL;;
            2) crearRespaldoR;;
            3) descRespaldo;;
            4) programarRespaldo;;
            5) ;;
            *) echo "Seleccione una opción valida";;
        esac
    done
}


#!/bin/bash

#dev/null desaparece los archivos (dentro de null)
# -z Comprueba si la variable está vacia
# -m Crea el directorio si no existe
# -s Establece el shell que utiliza el usuario
#IFS=: Separador de campos a través de :

grupoExiste() {
    getent group $1 > /dev/null
}

usuarioExiste() {
    getent passwd $1 > /dev/null
}



crearGrupo() {
    clear
    local gru
    read -p "Nombre del nuevo grupo: " gru

    if [[ -z "$gru" ]]; then
        echo "El nombre no puede estar vacío"

    elif grupoExiste "$gru"; then
        echo "El grupo ya existe"

    else
        groupadd $gru

        if [[ $? -eq 0 ]]; then
            echo "El grupo se ha creado correctamente"
        else
            echo "Ha ocurrido un problema, no se ha podido crear el grupo"
        fi
    fi
}


eliminarGrupo() {
    clear
    local gru
    read -rp "Nombre del grupo que se desea eliminar: " gru

    if [[ -z $gru ]]; then
        echo "Por favor seleccione el nombre del grupo que desee eliminar"

    elif ! grupoExiste $gru; then
        echo "El grupo no existe"
    else 
        groupdel $gru
        if [[ $? -eq 0 ]]; then
            echo "El grupo se ha eliminado correctamente"
        else
            echo "Ha ocurrido un error, el grupo no pudo ser eliminado"
        fi

    fi
}



verGruposusu() {
    clear
    
    read -p "Ingrese el nombre del usuario: " usu

    groups $usu
}

listarGrupos() {
    clear

    getent group | cut -d: -f1
}


agregarquitarUsu(){
    clear

    echo "---Seleccióne una opción---"
    echo "1. Agregar usuario a un grupo"
    echo "2. Eliminar a un usuario de un grupo"
    echo "----------------------------"

    read -p "Opción: " opc

    read -p "Seleccione al usuario: " usu
    read -p "Seleccione al grupo: " gru

    if [[ -z $usu || -z $gru ]]; then

        echo "Los campos no pueden quedar vacios"

    elif ! usuarioExiste $usu; then

        echo "El usuario no existe"

    elif ! grupoExiste $gru; then

        echo "El grupo no existe"

    else

        if [[ $opc -eq 1 ]]; then
            usermod -aG $gru $usu
        elif [[ $opc -eq 2 ]]; then
            gpasswd -d $usu $gru
        fi

    fi
    
}


grupos() {

clear
local opc=77

while [[ $opc -ne 6 ]]; do

    echo "---Seleccione una opcion---"
    echo "1. Crear grupo"
    echo "2. Eliminar grupo"
    echo "3. Listar los grupos de un usuario"
    echo "4. Listar todos los grupos"
    echo "5. Agregar o eliminar usuario de un grupo secundario"
    echo "6. Salir"
    echo "---------------------------"

    read -p "opción: " opc

    case $opc in
        1) crearGrupo;;
        2) eliminarGrupo;;
        3) verGruposusu;;
        4) listarGrupos;;
        5) agregarquitarUsu;;
        6);;
        *) echo "Seleccióne una opción valida";;
    esac
done
}


#!/bin/bash

agregarServicio(){
    local ser
    read -p "Ingrese el nombre del servicio o n° de puerto: " ser

    sudo ufw allow $ser
}

bloquearServicio(){
    local ser
    read -p "Ingrese el nombre del servicio o n° de puerto: " ser

    sudo ufw deny $ser
}

agregarIP(){
    local ip
    read -p "Ingrese la IP que desea agregar: " ip

    sudo ufw add from $ip
}   

bloquearIP(){
    local ip
    read -p "Ingrese la IP que desea bloquear: " ip

    sudo ufw deny from $ip
}

desbloquearIP(){
    local ip
    read -p "Ingrese la IP que desea desbloquear: " ip

    sudo ufw delete deny from $ip 
}

listaServicios(){
    sudo ufw status
}



firewall(){
    local opc=99

    echo "---Seleccione una opción---"
    echo "1. Agregar servicios"
    echo "2. Bloquear servicios"
    echo "3. Agregar IP"
    echo "4. Bloquear IP"
    echo "5. Desbloquear IP"
    echo "6. Lista de servicios"
    echo "---------------------------"


    while [[ opc -ne 7 ]]; do

    read -p "Opción: " opc

    case $opc in
        1)agregarServicio;;
        2)bloquearServicio;;
        3)agregarIP;;
        4)bloquearIP;;
        5)desbloquearIP;;
        6)listaServicios;;
        7) ;;
        *) echo "Seleccióne una opción valida";;
    esac
    done
}
