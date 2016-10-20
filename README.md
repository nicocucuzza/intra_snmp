# intra_snmp
# Requisitos de uso:
#
# - Paquete snmpd
# - PHP >= 7.0
# - PHP COmposer
# - Apache >= 2.2.4
# 
# Instalación
#
# git clone https://github.com/nicocucuzza/intra_snmp.git
# mv intra_snmp/src/web < /var/www/ > 
# mv .htaccess /var/www/.
# sudo service apache2 restart
# sudo service snmpd restart
#
# Se debe modificar el archivo .htaccess conforme donde se haya instalado el código de la aplicación, archivo RestController.php
# La ejecución se básica se puede hacer por medio de un navegador accediendo a http://127.0.0.1/snmp
# El archivo UnitTest.php posee algunos test unitarios de funcionalidad, para ejecutarlo requiere la instalación del módulo PHPUnit
