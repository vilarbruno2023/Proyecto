#!/bin/bash

while true
do
echo "------ Gestion: Grupo ------"
    echo "1. Crear grupo"
    echo "2. Eliminar grupo"
    echo "3. Listar grupos"
    echo "4. Salir"
    read -p "Seleccione una opción: " opcion

    case $opcion in
        1)
            read -p "Nombre del grupo: " grupo
            sudo groupadd "$grupo"
            echo "Grupo creado."
            ;;
        2)
            read -p "Nombre del grupo a eliminar: " grupo
            sudo groupdel "$grupo"
            echo "Grupo eliminado."
            ;;
        3)
            cut -d: -f1 /etc/group
            ;;
        4)
            break
            ;;
        *)
            echo "Opción no válida."
            ;;
    esac

    echo
done