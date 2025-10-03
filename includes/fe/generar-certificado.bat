

openssl genrsa -out KoiFacturaElectronicaNCNTS.key 2048
openssl req -new -key KoiFacturaElectronicaNCNTS.key -sha256 -subj "/CN=koifacturaelectronica/serialNumber=CUIT 30716182815" -out KoiFacturaElectronicaNCNTS.csr
pause

