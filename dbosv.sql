CREATE DATABASE dbosv;
use dbosv;


CREATE TABLE tb_nube_comprobantes (
    id_comprobante INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cuit CHAR(11) NOT NULL,              -- CUIT solo números
    nro_factura VARCHAR(40) NOT NULL,    -- Número de factura
    periodo CHAR(7) NOT NULL,            -- Formato YYYYMM 
    nombre_archivo VARCHAR(255) NOT NULL, -- Nombre lógico del archivo
    fecha_subida DATETIME NOT NULL,
	cod_usuario_registra INT NOT NULL
)  ;


INSERT INTO  `tb_menus` (`menu_descripcion`, `menu_icono`, `menu_link`, `menu_grupo`, `menu_principal`, `menu_orden`, `menu_estado`, `tipo_ruta`) VALUES ('Nube Comprobantes', '-', '/facturacion/nube-comprobantes', 'GM24', '0', '7', '1', '-');
