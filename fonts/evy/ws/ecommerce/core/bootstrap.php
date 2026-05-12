<?php

setup_autoloader();

function setup_autoloader() {
	Ecommerce_Core_Autoloader::add_namespace('Ecommerce_Core', COREPATH);

	Ecommerce_Core_Autoloader::add_classes(array(
		 'Ecommerce_Core_Controller'				=> COREPATH . 'controller.php',
		 'Ecommerce_Core_Controller_Rest'			=> COREPATH . 'controller/rest.php',

		 'Ecommerce_Core_Ecommerce'						=> COREPATH . 'ecommerce.php',
		 'Ecommerce_Core_EcommerceException'			=> COREPATH . 'ecommerce.php',

		 'Ecommerce_Core_Format'					=> COREPATH . 'format.php',

		 'Ecommerce_Core_HttpException'				=> COREPATH . 'httpexceptions.php',
		 'Ecommerce_Core_HttpNotFoundException'		=> COREPATH . 'httpexceptions.php',
		 'Ecommerce_Core_HttpServerErrorException'	=> COREPATH . 'httpexceptions.php',

		 'Ecommerce_Core_Input'						=> COREPATH . 'input.php',

		 'Ecommerce_Core_Request'					=> COREPATH . 'request.php',

		 'Ecommerce_Core_Response'					=> COREPATH . 'response.php',

		 'Ecommerce_Core_Route'						=> COREPATH . 'route.php',
		 'Ecommerce_Core_Router'					=> COREPATH . 'router.php',

		 'Ecommerce_Core_Uri'						=> COREPATH . 'uri.php',
	));
/*
	Ecommerce_Core_Autoloader::add_classes(array(
		 'Fuel_Core_Agent'           => COREPATH.'agent.php',

		 'Fuel_Core_Arr'             => COREPATH.'arr.php',

		 'Fuel_Core_Asset'           => COREPATH.'asset.php',
		 'Fuel_Core_Asset_Instance'  => COREPATH.'asset/instance.php',

		 'Fuel_Core_Cache'                     => COREPATH.'cache.php',
		 'Fuel_Core_CacheNotFoundException'    => COREPATH.'cache/notfound.php',
		 'Fuel_Core_CacheExpiredException'     => COREPATH.'cache.php',
		 'Fuel_Core_Cache_Handler_Driver'      => COREPATH.'cache/handler/driver.php',
		 'Fuel_Core_Cache_Handler_Json'        => COREPATH.'cache/handler/json.php',
		 'Fuel_Core_Cache_Handler_Serialized'  => COREPATH.'cache/handler/serialized.php',
		 'Fuel_Core_Cache_Handler_String'      => COREPATH.'cache/handler/string.php',
		 'Fuel_Core_Cache_Storage_Driver'      => COREPATH.'cache/storage/driver.php',
		 'Fuel_Core_Cache_Storage_Apc'         => COREPATH.'cache/storage/apc.php',
		 'Fuel_Core_Cache_Storage_File'        => COREPATH.'cache/storage/file.php',
		 'Fuel_Core_Cache_Storage_Memcached'   => COREPATH.'cache/storage/memcached.php',
		 'Fuel_Core_Cache_Storage_Redis'       => COREPATH.'cache/storage/redis.php',

		 'Fuel_Core_Config'               => COREPATH.'config.php',
		 'Fuel_Core_ConfigException'      => COREPATH.'config.php',
		 'Fuel_Core_Config_File'          => COREPATH.'config/file.php',
		 'Fuel_Core_Config_Ini'           => COREPATH.'config/ini.php',
		 'Fuel_Core_Config_Json'          => COREPATH.'config/json.php',
		 'Fuel_Core_Config_Interface'     => COREPATH.'config/interface.php',
		 'Fuel_Core_Config_Php'           => COREPATH.'config/php.php',
		 'Fuel_Core_Config_Yml'          => COREPATH.'config/yml.php',

		 'Fuel_Core_Controller'           => COREPATH.'controller.php',
		 'Fuel_Core_Controller_Rest'      => COREPATH.'controller/rest.php',
		 'Fuel_Core_Controller_Template'  => COREPATH.'controller/template.php',
		 'Fuel_Core_Controller_Hybrid'    => COREPATH.'controller/hybrid.php',

		 'Fuel_Core_Cookie'               => COREPATH.'cookie.php',

		 'Fuel_Core_DB'      => COREPATH.'db.php',
		 'Fuel_Core_DBUtil'  => COREPATH.'dbutil.php',

		 'Fuel_Core_Database_Connection'            => COREPATH.'database/connection.php',
		 'Fuel_Core_Database_Exception'             => COREPATH.'database/exception.php',
		 'Fuel_Core_Database_Expression'            => COREPATH.'database/expression.php',
		 'Fuel_Core_Database_Pdo_Connection'        => COREPATH.'database/pdo/connection.php',
		 'Fuel_Core_Database_Query'                 => COREPATH.'database/query.php',
		 'Fuel_Core_Database_Query_Builder'         => COREPATH.'database/query/builder.php',
		 'Fuel_Core_Database_Query_Builder_Insert'  => COREPATH.'database/query/builder/insert.php',
		 'Fuel_Core_Database_Query_Builder_Delete'  => COREPATH.'database/query/builder/delete.php',
		 'Fuel_Core_Database_Query_Builder_Update'  => COREPATH.'database/query/builder/update.php',
		 'Fuel_Core_Database_Query_Builder_Select'  => COREPATH.'database/query/builder/select.php',
		 'Fuel_Core_Database_Query_Builder_Where'   => COREPATH.'database/query/builder/where.php',
		 'Fuel_Core_Database_Query_Builder_Join'    => COREPATH.'database/query/builder/join.php',
		 'Fuel_Core_Database_Result'                => COREPATH.'database/result.php',
		 'Fuel_Core_Database_Result_Cached'         => COREPATH.'database/result/cached.php',
		 'Fuel_Core_Database_Mysql_Connection'      => COREPATH.'database/mysql/connection.php',
		 'Fuel_Core_Database_MySQL_Result'          => COREPATH.'database/mysql/result.php',
		 'Fuel_Core_Database_Mysqli_Connection'     => COREPATH.'database/mysqli/connection.php',
		 'Fuel_Core_Database_MySQLi_Result'         => COREPATH.'database/mysqli/result.php',

		 'Fuel_Core_Fuel'           => COREPATH.'fuel.php',
		 'Fuel_Core_FuelException'  => COREPATH.'fuel.php',

		 'Fuel_Core_Finder'         => COREPATH.'finder.php',

		 'Fuel_Core_Date' => COREPATH.'date.php',

		 'Fuel_Core_Debug'   => COREPATH.'debug.php',

		 'Fuel_Core_Cli'     => COREPATH.'cli.php',

		 'Fuel_Core_Crypt'   => COREPATH.'crypt.php',

		 'Fuel_Core_Event'            => COREPATH.'event.php',
		 'Fuel_Core_Event_Instance'   => COREPATH.'event/instance.php',

		 'Fuel_Core_Error'               => COREPATH.'error.php',
		 'Fuel_Core_PhpErrorException'   => COREPATH.'error.php',

		 'Fuel_Core_Format'  => COREPATH.'format.php',

		 'Fuel_Core_Fieldset'        => COREPATH.'fieldset.php',
		 'Fuel_Core_Fieldset_Field'  => COREPATH.'fieldset/field.php',

		 'Fuel_Core_File'                    => COREPATH.'file.php',
		 'Fuel_Core_FileAccessException'     => COREPATH.'file.php',
		 'Fuel_Core_OutsideAreaException'    => COREPATH.'file.php',
		 'Fuel_Core_InvalidPathException'    => COREPATH.'file.php',
		 'Fuel_Core_File_Area'               => COREPATH.'file/area.php',
		 'Fuel_Core_File_Handler_File'       => COREPATH.'file/handler/file.php',
		 'Fuel_Core_File_Handler_Directory'  => COREPATH.'file/handler/directory.php',

		 'Fuel_Core_Form'           => COREPATH.'form.php',
		 'Fuel_Core_Form_Instance'  => COREPATH.'form/instance.php',

		 'Fuel_Core_Ftp'                     => COREPATH.'ftp.php',
		 'Fuel_Core_FtpConnectionException'  => COREPATH.'ftp.php',
		 'Fuel_Core_FtpFileAccessException'  => COREPATH.'ftp.php',

		 'Fuel_Core_HttpException'             => COREPATH.'httpexception.php',
		 'Fuel_Core_HttpNotFoundException'     => COREPATH.'httpexceptions.php',
		 'Fuel_Core_HttpServerErrorException'  => COREPATH.'httpexceptions.php',

		 'Fuel_Core_Html'  => COREPATH.'html.php',

		 'Fuel_Core_Image'              => COREPATH.'image.php',
		 'Fuel_Core_Image_Driver'       => COREPATH.'image/driver.php',
		 'Fuel_Core_Image_Gd'           => COREPATH.'image/gd.php',
		 'Fuel_Core_Image_Imagemagick'  => COREPATH.'image/imagemagick.php',
		 'Fuel_Core_Image_Imagick'      => COREPATH.'image/imagick.php',

		 'Fuel_Core_Inflector'  => COREPATH.'inflector.php',

		 'Fuel_Core_Input'      => COREPATH.'input.php',

		 'Fuel_Core_Lang'               => COREPATH.'lang.php',
		 'Fuel_Core_LangException'      => COREPATH.'lang.php',
		 'Fuel_Core_Lang_File'          => COREPATH.'lang/file.php',
		 'Fuel_Core_Lang_Ini'           => COREPATH.'lang/ini.php',
		 'Fuel_Core_Lang_Json'          => COREPATH.'lang/json.php',
		 'Fuel_Core_Lang_Interface'     => COREPATH.'lang/interface.php',
		 'Fuel_Core_Lang_Php'           => COREPATH.'lang/php.php',
		 'Fuel_Core_Lang_Yml'           => COREPATH.'lang/yml.php',

		 'Fuel_Core_Markdown'   => COREPATH.'markdown.php',

		 'Fuel_Core_Migrate'    => COREPATH.'migrate.php',

		 'Fuel_Core_Model'      => COREPATH.'model.php',
		 'Fuel_Core_Model_Crud' => COREPATH.'model/crud.php',

		 'Fuel_Core_Module'                    => COREPATH.'module.php',
		 'Fuel_Core_ModuleNotFoundException'   => COREPATH.'module.php',

		 'Fuel_Core_Mongo_Db'           => COREPATH.'mongo/db.php',
		 'Fuel_Core_Mongo_DbException'  => COREPATH.'mongo/db.php',

		 'Fuel_Core_Output'               => COREPATH.'output.php',

		 'Fuel_Core_Package'                   => COREPATH.'package.php',
		 'Fuel_Core_PackageNotFoundException'  => COREPATH.'package.php',

		 'Fuel_Core_Pagination'           => COREPATH.'pagination.php',

		 'Fuel_Core_Profiler'             => COREPATH.'profiler.php',

		 'Fuel_Core_Request'                 => COREPATH.'request.php',
		 'Fuel_Core_Request_Driver'          => COREPATH.'request/driver.php',
		 'Fuel_Core_RequestException'        => COREPATH.'request/driver.php',
		 'Fuel_Core_RequestStatusException'  => COREPATH.'request/driver.php',
		 'Fuel_Core_Request_Curl'            => COREPATH.'request/curl.php',
		 'Fuel_Core_Request_Soap'            => COREPATH.'request/soap.php',

		 'Fuel_Core_Redis'                   => COREPATH.'redis.php',
		 'Fuel_Core_RedisException'          => COREPATH.'redis.php',

		 'Fuel_Core_Response'  => COREPATH.'response.php',

		 'Fuel_Core_Route'     => COREPATH.'route.php',
		 'Fuel_Core_Router'    => COREPATH.'router.php',

		 'Fuel_Core_Security'  => COREPATH.'security.php',

		 'Fuel_Core_Session'            => COREPATH.'session.php',
		 'Fuel_Core_Session_Driver'     => COREPATH.'session/driver.php',
		 'Fuel_Core_Session_Db'         => COREPATH.'session/db.php',
		 'Fuel_Core_Session_Cookie'     => COREPATH.'session/cookie.php',
		 'Fuel_Core_Session_File'       => COREPATH.'session/file.php',
		 'Fuel_Core_Session_Memcached'  => COREPATH.'session/memcached.php',
		 'Fuel_Core_Session_Redis'      => COREPATH.'session/redis.php',
		 'Fuel_Core_Session_Exception'  => COREPATH.'session/exception.php',

		 'Fuel_Core_Num'       => COREPATH.'num.php',

		 'Fuel_Core_Str'       => COREPATH.'str.php',

		 'Fuel_Core_TestCase'  => COREPATH.'testcase.php',

		 'Fuel_Core_Theme'          => COREPATH.'theme.php',
		 'Fuel_Core_ThemeException' => COREPATH.'theme.php',

		 'Fuel_Core_Uri'       => COREPATH.'uri.php',

		 'Fuel_Core_Unzip'     => COREPATH.'unzip.php',

		 'Fuel_Core_Upload'    => COREPATH.'upload.php',

		 'Fuel_Core_Validation'        => COREPATH.'validation.php',
		 'Fuel_Core_Validation_Error'  => COREPATH.'validation/error.php',

		 'Fuel_Core_View'       => COREPATH.'view.php',
		 'Fuel_Core_ViewModel'  => COREPATH.'viewmodel.php',
	));
*/
};
