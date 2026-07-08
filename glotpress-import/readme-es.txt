=== Plogins Add-Ons - Product Options for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, product options, product addons, extra fields, custom product fields
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Requiere complementos: woocommerce
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Añade opciones de productos de WooCommerce, complementos de productos y campos de productos personalizados antes de agregarlos al carrito.

== Description ==

Los complementos permiten a los propietarios de tiendas ofrecer opciones de productos WooCommerce, complementos de productos y campos de productos personalizados que los clientes eligen antes de añadir un producto al carrito: envoltorio de regalo, un mensaje grabado, una garantía extendida o una elección de color.

Para cada producto, define una lista de complementos en el editor de productos de WooCommerce. Cada complemento tiene una etiqueta, un tipo de campo, un indicador obligatorio opcional y un precio opcional.

* <strong>Tipos de campo</strong>, texto sin formato, una casilla de verificación o un menú desplegable de selección.
* <strong>Límites de texto</strong>, establece longitudes mínimas y máximas de caracteres para las opciones de productos de texto.
* <strong>Deltas de precios</strong>, asigna un precio a un complemento (o a cada opción seleccionada); el monto se añade al total de la línea del carrito automáticamente.
* <strong>Gratis o de pago</strong>, deja el precio en cero para opciones gratuitas como un mensaje personalizado.
* <strong>Visualización del carrito y del pedido</strong>, las opciones del cliente aparecen en el carrito, al finalizar la compra y en el pedido.
* <strong>Mostrar configuración</strong>, elegir el encabezado del grupo, mostrar u ocultar los precios de las opciones, alternar el asterisco del campo obligatorio y envolver las opciones en una tarjeta con borde, todo desde la página de configuración de Complementos.

Las definiciones de complementos se almacenan como metaestándar del producto, sin tablas de bases de datos personalizadas, por lo que el complemento en sí sigue siendo pequeño y rápido.

La configuración se encuentra en <strong>WooCommerce → Complementos</strong>. Al eliminar el complemento se limpian sus propias opciones; sus definiciones por producto se mantienen como meta del producto, por lo que la reinstalación las restaura.

El código se desarrolla abiertamente en https://github.com/wppoland/plogins-addons, ese es el lugar para informar un error o sugerir un tipo de campo que le gustaría ver.

== Installation ==

1. Cargue el complemento en `/wp-content/plugins/plogins-addons`, o instálelo a través de Complementos → Añadir nuevo.
2. Actívalo. WooCommerce debe estar activo.
3. Edite un producto, abra la pestaña <strong>Complementos</strong> en el panel de datos del producto y añade sus opciones.

== Frequently Asked Questions ==

= Documentation and links =

* <strong>Documentación</strong> - https://plogins.com/es/plogins-addons/docs/
* <strong>Página de complementos</strong> - https://plogins.com/es/plogins-addons/
* <strong>Código fuente</strong> - https://github.com/wppoland/plogins-addons
* <strong>Informes de errores y solicitudes de funciones</strong> - https://github.com/wppoland/plogins-addons/issues


= Does it require WooCommerce? =

Sí. WooCommerce debe estar instalado y activo.

= Where do customers see the options? =

En la página de un solo producto, justo encima del botón Añadir al carrito. Sus selecciones luego se muestran en el carrito, al finalizar la compra y en el pedido.

= What field types are included? =

La versión gratuita incluye campos de texto, casillas de verificación y menús desplegables de selección. Cada campo puede ser gratuito o añadir un precio a la línea del carrito.

= Can a product option change the price? =

Sí. Añade un precio a la fila misma o a opciones seleccionadas individuales, y Add-Ons agregará esa cantidad a la línea del carrito de WooCommerce.

= Can I make a product option required? =

Sí. Marque la casilla Obligatorio para ver una opción y el producto no se podrá añadir al carrito hasta que el comprador lo complete.

= Can I limit text option length? =

Sí. Los complementos de texto pueden tener límites mínimos y máximos de caracteres. El escaparate muestra un contador en vivo y el servidor valida los mismos límites antes de agregarlos al carrito.

= Does it create custom database tables? =

No. Las definiciones de complementos se almacenan como meta del producto.

= Does it support file uploads or conditional logic? =

Esas son características PRO. Add-Ons FREE se centra en texto rápido, casillas de verificación y opciones de productos seleccionados.


= Does this plugin work on WordPress Multisite? =

Sí. Este complemento es compatible con WordPress Multisite. Activarlo en red o activarlo en sitios individuales; Cada sitio mantiene su propia configuración y datos.

== Screenshots ==

1. En el escaparate.
2. Configuración en el administrador de WordPress.
3. En un dispositivo móvil.
== External Services ==

Los complementos no se conectan a ningún servicio externo. No envía datos fuera de tu sitio y no carga scripts, fuentes o rastreadores remotos, su administrador y CSS/JS de escaparate se sirven desde la carpeta de complementos en su propio servidor. Las definiciones de sus complementos se almacenan como meta del producto (`_addons_definitions`) y la configuración de visualización en una sola opción (`addons_settings`), todo guardado en tu base de datos de WordPress.

== Changelog ==

= 1.0.1 =
* Primera versión estable.

= 0.3.1 =
* Renombrado a Plogins Add-Ons para WooCommerce para obtener un nombre de complemento más distintivo.

= 0.3.0 =
* Añade límites mínimos y máximos de caracteres para complementos de texto, con contadores de tienda y validación del lado del servidor.

= 0.2.0 =
* Añade un encabezado de grupo personalizable que se muestra encima de los campos complementarios en la página del producto.
* Añadir configuración de visualización: mostrar/ocultar precios de opciones, alternar el asterisco del campo obligatorio y un estilo de tarjeta con borde opcional.
* Añade una rutina de desinstalación que elimine las opciones propias del complemento (las definiciones de productos se conservan como meta del producto).

= 0.1.0 =
* Lanzamiento inicial.
