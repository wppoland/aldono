=== Plogins Add-Ons - Product Options for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, product options, product addons, extra fields, custom product fields
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Requiere plugins: woocommerce
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Añade opciones de producto de WooCommerce, complementos de producto y campos de producto personalizados antes de añadir al carrito.

== Description ==

Add-Ons permite a los propietarios de tiendas ofrecer opciones de producto de WooCommerce, complementos de producto y campos de producto personalizados que los clientes eligen antes de añadir un producto al carrito: envoltorio de regalo, un mensaje grabado, una garantía ampliada o una elección de color.

Para cada producto defines una lista de complementos en el editor de productos de WooCommerce. Cada complemento tiene una etiqueta, un tipo de campo, una marca opcional de «obligatorio» y un precio opcional.

* <strong>Tipos de campo</strong> — texto sin formato, una casilla de verificación o una lista desplegable.
* <strong>Límites de texto</strong> — establece el número mínimo y máximo de caracteres para las opciones de producto de tipo texto.
* <strong>Diferencias de precio</strong> — asigna un precio a un complemento (o a cada opción de la lista); el importe se suma automáticamente al total de la línea del carrito.
* <strong>Gratis o de pago</strong> — deja el precio a cero para las opciones gratuitas, como un mensaje personalizado.
* <strong>Visualización en el carrito y el pedido</strong> — las selecciones del cliente aparecen en el carrito, en el pago y en el pedido.
* <strong>Ajustes de visualización</strong> — elige el encabezado del grupo, muestra u oculta los precios de las opciones, activa o desactiva el asterisco del campo obligatorio y envuelve las opciones en una tarjeta con borde, todo desde la página de ajustes de Add-Ons.

Las definiciones de complementos se almacenan como metadatos estándar del producto, sin tablas de base de datos personalizadas, por lo que el plugin en sí sigue siendo pequeño y rápido.

Los ajustes están en <strong>WooCommerce → Complementos</strong>. Al eliminar el plugin se borran sus propias opciones; tus definiciones por producto se conservan como metadatos del producto, así que al reinstalarlo se restauran.

El código se desarrolla de forma abierta (código abierto) en https://github.com/wppoland/plogins-addons; ahí es donde puedes informar de un error o sugerir un tipo de campo que te gustaría ver.

== Installation ==

1. Sube el plugin a `/wp-content/plugins/plogins-addons` o instálalo desde Plugins → Añadir nuevo.
2. Actívalo. WooCommerce debe estar activo.
3. Edita un producto, abre la pestaña <strong>Complementos</strong> en el panel de datos del producto y añade tus opciones.

== Frequently Asked Questions ==

= Documentation and links =

* <strong>Documentación</strong> - https://plogins.com/es/plogins-addons/docs/
* <strong>Página del plugin</strong> - https://plogins.com/es/plogins-addons/
* <strong>Código fuente</strong> - https://github.com/wppoland/plogins-addons
* <strong>Informes de errores y peticiones de funciones</strong> - https://github.com/wppoland/plogins-addons/issues


= Does it require WooCommerce? =

Sí. WooCommerce debe estar instalado y activo.

= Where do customers see the options? =

En la página de producto individual, justo encima del botón Añadir al carrito. Sus selecciones aparecen después en el carrito, en el pago y en el pedido.

= What field types are included? =

La versión gratuita incluye campos de texto, casillas de verificación y listas desplegables. Cada campo puede ser gratuito o sumar un importe a la línea del carrito.

= Can a product option change the price? =

Sí. Añade un precio a la propia fila o a cada una de las opciones de la lista, y el plugin suma ese importe a la línea del carrito de WooCommerce.

= Can I make a product option required? =

Sí. Marca la casilla «Obligatorio» de una opción y el producto no se podrá añadir al carrito hasta que el comprador la complete.

= Can I limit text option length? =

Sí. Los complementos de texto pueden tener límites mínimos y máximos de caracteres. La tienda muestra un contador en vivo y el servidor valida los mismos límites antes de añadir al carrito.

= Does it create custom database tables? =

No. Las definiciones de complementos se almacenan como metadatos del producto.

= Does it support file uploads or conditional logic? =

Esas son funciones PRO. Add-Ons FREE se centra en opciones de producto rápidas: texto, casillas de verificación y listas desplegables.


= Does this plugin work on WordPress Multisite? =

Sí. Este plugin es compatible con WordPress Multisite. Actívalo en red o en sitios concretos; cada sitio conserva sus propios ajustes y datos.

== Screenshots ==

1. En la tienda.
2. Ajustes en la administración de WordPress.
3. En un dispositivo móvil.
== External Services ==

Add-Ons no se conecta a ningún servicio externo. No envía datos fuera de tu sitio y no carga scripts, fuentes ni rastreadores remotos: su CSS/JS de administración y de la tienda se sirven desde la carpeta del plugin en tu propio servidor. Tus definiciones de complementos se almacenan como metadatos del producto (`_addons_definitions`) y los ajustes de visualización en una sola opción (`addons_settings`), todo guardado en tu base de datos de WordPress.

== Translations ==

Plogins Add-Ons incluye traducciones al polaco, al alemán y al español para la interfaz del plugin. El dominio de texto es `plogins-addons`, así que los paquetes de idioma de WordPress.org también pueden sustituir o ampliar estas traducciones incluidas.

== Changelog ==

= 1.0.2 =
* Añadidas traducciones incluidas al polaco, al alemán y al español para la interfaz del plugin.

= 1.0.1 =
* Primera versión estable.

= 0.3.1 =
* Renombrado a Plogins Add-Ons para WooCommerce, para un nombre de plugin más distintivo.

= 0.3.0 =
* Añadidos límites mínimos y máximos de caracteres para los complementos de texto, con contadores en la tienda y validación en el servidor.

= 0.2.0 =
* Añadido un encabezado de grupo personalizable que se muestra encima de los campos de complementos en la página del producto.
* Añadidos ajustes de visualización: mostrar u ocultar los precios de las opciones, activar o desactivar el asterisco del campo obligatorio y un estilo opcional de tarjeta con borde.
* Añadida una rutina de desinstalación que elimina las opciones propias del plugin (las definiciones de producto se conservan como metadatos del producto).

= 0.1.0 =
* Lanzamiento inicial.
