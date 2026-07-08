=== Plogins Add-Ons - Product Options for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, product options, product addons, extra fields, custom product fields
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Erfordert Plugins: woocommerce
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Füge WooCommerce-Produktoptionen, Produkt-Add-ons und benutzerdefinierte Produktfelder hinzu, bevor du sie in den Warenkorb legen.

== Description ==

Mit Add-Ons können Ladenbesitzer WooCommerce-Produktoptionen, Produkt-Add-Ons und benutzerdefinierte Produktfelder anbieten, die Kunden auswählen, bevor sie ein Produkt in den Warenkorb legen: Geschenkverpackung, eine Gravurnachricht, eine erweiterte Garantie oder eine Farbauswahl.

Für jedes Produkt definiere im WooCommerce-Produkteditor eine Liste von Add-ons. Jedes Add-on verfügt über eine Beschriftung, einen Feldtyp, eine optionale Pflichtkennzeichnung und einen optionalen Preis.

* <strong>Feldtypen</strong>, einfacher Text, ein Kontrollkästchen oder ein Auswahl-Dropdown.
* <strong>Textgrenzen</strong>, lege minimale und maximale Zeichenlängen für Textproduktoptionen fest.
* <strong>Preisdeltas</strong>, gib einem Add-on (oder jeder ausgewählten Option) einen Preis; Der Betrag wird automatisch zur Gesamtsumme der Warenkorbzeile hinzugefügt.
* <strong>Kostenlos oder kostenpflichtig</strong>, lass den Preis für kostenlose Optionen wie eine personalisierte Nachricht bei Null.
* <strong>Warenkorb- und Bestellanzeige</strong>: Die Auswahlmöglichkeiten des Kunden werden im Warenkorb, an der Kasse und in der Bestellung angezeigt.
* <strong>Anzeigeeinstellungen</strong>, Gruppenüberschrift auswählen, Optionspreise ein- oder ausblenden, das Sternchen im Pflichtfeld umschalten und die Optionen in eine umrandete Karte einschließen – alles auf der Seite mit den Add-On-Einstellungen.

Add-on-Definitionen werden als Standardprodukt-Meta und nicht als benutzerdefinierte Datenbanktabellen gespeichert, sodass das Plugin selbst klein und schnell bleibt.

Die Einstellungen findest du unter <strong>WooCommerce → Add-Ons</strong>. Durch das Entfernen des Plugins werden seine eigenen Optionen bereinigt; deine produktspezifischen Definitionen bleiben als Produktmeta erhalten und werden daher bei einer Neuinstallation wiederhergestellt.

Der Code wird öffentlich unter https://github.com/wppoland/plogins-addons entwickelt. Dort kannst du einen Fehler melden oder einen Feldtyp vorschlagen, den du sehen möchten.

== Installation ==

1. Lade das Plugin nach „/wp-content/plugins/plogins-addons“ hoch oder installiere es über Plugins → Neu hinzufügen.
2. Aktiviere es. WooCommerce muss aktiv sein.
3. Bearbeite ein Produkt, öffne die Registerkarte <strong>Add-Ons</strong> im Produktdatenbereich und füge deine Optionen hinzu.

== Frequently Asked Questions ==

= Documentation and links =

* <strong>Dokumentation</strong> - https://plogins.com/de/plogins-addons/docs/
* <strong>Plugin-Seite</strong> - https://plogins.com/de/plogins-addons/
* <strong>Quellcode</strong> – https://github.com/wppoland/plogins-addons
* <strong>Fehlerberichte und Funktionsanfragen</strong> – https://github.com/wppoland/plogins-addons/issues


= Does it require WooCommerce? =

Ja. WooCommerce muss installiert und aktiv sein.

= Where do customers see the options? =

Auf der einzelnen Produktseite direkt über der Schaltfläche „In den Warenkorb“. deine Auswahl wird dann im Warenkorb, an der Kasse und in der Bestellung angezeigt.

= What field types are included? =

Die kostenlose Version umfasst Textfelder, Kontrollkästchen und Auswahl-Dropdowns. Jedes Feld kann kostenlos sein oder der Warenkorbzeile einen Preis hinzufügen.

= Can a product option change the price? =

Ja. Füge einen Preis zur Zeile selbst oder zu einzelnen Auswahlmöglichkeiten hinzu, und Add-Ons fügt diesen Betrag der WooCommerce-Warenkorbzeile hinzu.

= Can I make a product option required? =

Ja. Aktiviere das Kontrollkästchen „Erforderlich“ für eine Option. Das Produkt kann dem Warenkorb erst hinzugefügt werden, wenn der Käufer es abgeschlossen hat.

= Can I limit text option length? =

Ja. Text-Add-ons können minimale und maximale Zeichenbeschränkungen haben. Die Storefront zeigt einen Live-Zähler und der Server validiert die gleichen Limits, bevor er in den Warenkorb gelegt wird.

= Does it create custom database tables? =

Nein. Add-on-Definitionen werden als Produktmeta gespeichert.

= Does it support file uploads or conditional logic? =

Das sind PRO-Funktionen. Add-Ons FREE konzentriert sich auf Schnelltext, Kontrollkästchen und ausgewählte Produktoptionen.


= Does this plugin work on WordPress Multisite? =

Ja. Dieses Plugin ist mit WordPress Multisite kompatibel. Aktiviere es im Netzwerk oder auf einzelnen Websites. Jede Site behält ihre eigenen Einstellungen und Daten.

== Screenshots ==

1. Im Schaufenster.
2. Einstellungen im WordPress-Admin.
3. Auf einem mobilen Gerät.
== External Services ==

Add-Ons stellen keine Verbindung zu externen Diensten her. Es sendet keine Daten von deiner Website und lädt keine Remote-Skripte, Schriftarten oder Tracker. Sein Admin- und Storefront-CSS/JS wird vom Plugin-Ordner auf deinem eigenen Server bereitgestellt. deine Add-on-Definitionen werden als Produktmeta („_addons_definitions“) und die Anzeigeeinstellungen in einer einzigen Option („addons_settings“) gespeichert, allesamt in deiner WordPress-Datenbank.

== Changelog ==

= 1.0.1 =
* Erste stabile Version.

= 0.3.1 =
* Für einen eindeutigeren Plugin-Namen in Plogins Add-Ons für WooCommerce umbenannt.

= 0.3.0 =
* Füge minimale und maximale Zeichenbeschränkungen für Text-Add-ons hinzu, mit Storefront-Zählern und serverseitiger Validierung.

= 0.2.0 =
* Füge eine anpassbare Gruppenüberschrift hinzu, die über den Add-on-Feldern auf der Produktseite angezeigt wird.
* Anzeigeeinstellungen hinzufügen: Optionspreise ein-/ausblenden, das Sternchen im Pflichtfeld umschalten und optional einen umrandeten Kartenstil hinzufügen.
* Füge eine Deinstallationsroutine hinzu, die die eigenen Optionen des Plugins entfernt (Produktdefinitionen bleiben als Produktmeta erhalten).

= 0.1.0 =
* Erstveröffentlichung.
