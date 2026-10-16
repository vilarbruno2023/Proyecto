#!/bin/bash

while true
do
echo "------ Gestion ------"
    echo "1. Crear usuario"
    echo "2. Eliminar usuario"
    echo "3. Modificar contraseña" 
    echo "4. Crear grupo"
    echo "5. Eliminar grupo"
    echo "6. Listar grupos"
    echo "7. Salir"
    echo
    read -p "Seleccione una opción: " opcion
    case $opcion in
    1) 
        read -p "Ingrese nombre de usuario: " usuario
        sudo useradd $usuario
        sudo passwd $usuario
        echo "Usuario: $usuario creado correctamente." 
        ;;
    2) 
        read -p "Ingrese usuario a eliminar: " 
        usuario sudo userdel -r $usuario 
        echo "Usuario: $usuario eliminado." 
        ;;
    3) 
        read -p "Ingrese usuario: " usuario
        sudo passwd $usuario 
        ;;
    4)
        read -p "Nombre del grupo: " grupo
        sudo groupadd "$grupo"
        echo "Grupo creado."
        ;;
    5)
        read -p "Nombre del grupo a eliminar: " grupo
        sudo groupdel "$grupo"
        echo "Grupo eliminado."
        ;;
    6)
        cut -d: -f1 /etc/group
        ;;
    7)
        break
        ;;
    *)
        echo "Opción no válida."
        ;;
    esac

    echo
done