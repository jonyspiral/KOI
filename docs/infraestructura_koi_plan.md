## INFRAESTRUCTURA KOI – MAPA DEFINITIVO (MAYO 2025)
sERVIDORES FISICOS
1_ 
Marca / Modelo	HP DL380 G5 2x(Intel Xeon E5335 @ 2.00GHz) 16GB (8x2GB)
Hostname	HYPERV01.spiral.local
SN / Product ID	2UX74604ND / 433525-001 
Sistema operativo	Windows Server 2012 R2
Arreglo Discos	
	
IP	192.168.2.201
	
RDP	192.168.2.201:33201
Acceso TV	918 894 612 :: Root,350
Usuario local	administrador :: Root,2317!
	
Antivirus 	-
Funcion	HYPERVISOR
	
iLO 2	192.168.2.193 
	Administrator  ::  Root,2317!
	Spiral Shoes  ::  SpiralServix

    
2_	
Marca / Modelo	HP DL380 G5 2x(Intel Xeon E5335 @ 2.00GHz) 16GB (8x2GB)
Hostname	HYPERV02.spiral.local
SN / Product ID	USE842N3RJ / 391835-B21 
Sistema operativo	Windows Server 2012 R2
Arreglo Discos	
	
IP	192.168.2.202
RDP	192.168.2.202
Acceso TV	918 805 848 :: Root,350
Usuario local	HYPERV02\Administrador :: Root,2317!
	
Antivirus 	-
Funcion	HYPERVISOR
	
iLO 2	192.168.2.196
	Administrator :: Root,2317!
	ILOHP2
	
### ⚖️ SERVIDORES

| N° | Nombre | IP Interna    | Sistema             | Rol / Uso                        |
| -- | ------ | ------------- | ------------------- | -------------------------------- |
| 1  | SERVER | 192.168.2.100 | Windows Server 2003 | KOI Clásico (Spiral / Encinitas) |
| 2  |UBUNTU   | 192.168.2.210 | Ubuntu Server       | KOI2 Producción + Desarrollo     |

### 🌐 DOMINIOS / PUERTOS / APLICACIONES

| Subdominio                | IP Interna    | Puerto | Rol / Aplicación        | Servidor |
| ------------------------- | ------------- | ------ | ----------------------- | -------- |
| koi.spiralshoes.com       | 192.168.2.100 | 80     | KOI ENCINITAS           | SERVER   |
| koi.spiralshoes.com:8181  | 192.168.2.100 | 8181   | KOI SPIRAL              | SERVER   |
| koi.spiralshoes.com:8191  | 192.168.2.100 | 8191   | KOI DESARROLLO (legacy) | SERVER   |
| koi2.spiralshoes.com:8081 | 192.168.2.210 | 8081   | KOI2 PRODUCCIÓN         | KOI2     |
| devkoi2.spiralshoes.com   | 192.168.2.210 | 8191   | KOI2 DESARROLLO Laravel | KOI2     |

### 🔒 SSL / HTTPS CONFIGURADO

| Dominio                 | SSL Certificado Requerido | Estado Actual           |
| ----------------------- | ------------------------- | ----------------------- |
| koi.spiralshoes.com     | Sí (Let's Encrypt)        | Pendiente               |
| koi2.spiralshoes.com    | Sí (Let's Encrypt)        | ✅ Emitido y funcionando |
| devkoi2.spiralshoes.com | Sí (Let's Encrypt)        | Pendiente               |

⚠️ **IMPORTANTE**: Mercado Libre **no acepta** dominios con puertos no estándar (como `:8081` o `:8191`) para callbacks, notificaciones o integración OAuth. Por eso, es obligatorio que `koi2.spiralshoes.com` tenga acceso por HTTPS estándar (puerto 443) mediante **reverse proxy con NGINX**.

### 📊 CONFIGURACIÓN FINALIZADA

#### 🛍️ MikroTik (NAT)

* [x] Redirección puerto 80 a 192.168.2.100 (koi.spiralshoes.com)
* [x] Redirección puerto 8081 a 192.168.2.210 (koi2.spiralshoes.com)
* [x] Redirección temporal del puerto 80 a 192.168.2.210 para emisión del SSL koi2
* [x] Restaurada la regla NAT original
* [x] Reordenada regla NAT koi.spiralshoes.com a posición 0
* [x] Redirección NAT puerto 443 a 192.168.2.210 activa (SSL koi2)

#### 🌐 DNS (Cloudflare / MikroTik)

* [x] Proxy desactivado temporalmente en Cloudflare
* [x] DNS interno resuelve koi2.spiralshoes.com → 192.168.2.210 (entrada estática MikroTik)

#### 📚 Apache (servidor KOI2)

* [x] VirtualHost definido en puerto 8081
* [x] Apache activo y sirviendo Laravel KOI2 en `http://localhost:8081`

#### ✨ Certbot (SSL)

* [x] Certificado Let’s Encrypt emitido correctamente con `--standalone`
* [ ] Pendiente: automatizar renovación con `cron` mensual

#### 🌐 Reverse Proxy NGINX (KOI2)

* [x] NGINX instalado y en ejecución
* [x] Redirección HTTP → HTTPS en puerto 80
* [x] Redirección HTTPS a Apache:8081
* [x] Certificado SSL configurado y funcionando

### 📊 USO ESPERADO DE CADA URL (INTERNET)

| URL                                                                  | Sistema que atiende   | Descripción Funcional              |
| -------------------------------------------------------------------- | --------------------- | ---------------------------------- |
| [https://koi.spiralshoes.com](https://koi.spiralshoes.com)           | KOI ENCINITAS         | Sistema administrativo actual      |
| [https://koi.spiralshoes.com:8181](https://koi.spiralshoes.com:8181) | KOI SPIRAL            | Sistema exclusivo Spiral Shoes     |
| [https://koi.spiralshoes.com:8191](https://koi.spiralshoes.com:8191) | KOI DESARROLLO legacy | Sistema de pruebas viejo           |
| [http://koi2.spiralshoes.com:8081](http://koi2.spiralshoes.com:8081) | KOI2 PRODUCCIÓN       | Sistema Laravel KOI2 Producción    |
| [https://koi2.spiralshoes.com](https://koi2.spiralshoes.com)         | KOI2 PRODUCCIÓN       | KOI2 Producción con SSL            |
| [https://devkoi2.spiralshoes.com](https://devkoi2.spiralshoes.com)   | KOI2 DESARROLLO       | Sistema Laravel en entorno de test |

---

Este archivo puede ser usado como referencia constante para validar que todo se mantenga alineado con los objetivos originales. Actualizalo con tildes y observaciones conforme avancemos.

Solicitá respaldo antes de ejecutar cambios estructurales.
