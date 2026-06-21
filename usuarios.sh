#!/bin/bash
echo "------ Gestion: Usuario ------"
echo "1. Crear usuario"
echo "2. Eliminar usuario"
echo "3. Modificar contraseña" 
read -p "Seleccione una opción: " opc
case $opc in
1) 
    read -p "Ingrese nombre de usuario: " usuario
    sudo useradd $usuario
    sudo passwd $usuario
    echo "Usuario: $usuario creado correctamente." 
    ;;
2) read -p "Ingrese usuario a eliminar: " 
    usuario sudo userdel -r $usuario 
    echo "Usuario: $usuario eliminado." 
    ;;
3) read -p "Ingrese usuario: " usuario
    sudo passwd $usuario 
    ;;
*) 
    echo "Opción inválida." ;; 
esac
;;