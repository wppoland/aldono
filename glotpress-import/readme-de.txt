=== Plogins Add-Ons - Product Options for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, product options, product addons, extra fields, custom product fields
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Erfordert Plugins: woocommerce
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Füge vor dem Hinzufügen zum Warenkorb WooCommerce-Produktoptionen, Produkt-Add-ons und benutzerdefinierte Produktfelder hinzu.

== Description ==

Mit Add-Ons kannst du als Shop-Betreiber WooCommerce-Produktoptionen, Produkt-Add-ons und benutzerdefinierte Produktfelder anbieten, die die Kundschaft auswählt, bevor sie ein Produkt in den Warenkorb legt: Geschenkverpackung, eine Gravur, eine erweiterte Garantie oder eine Farbauswahl.

Für jedes Produkt definierst du im WooCommerce-Produkteditor eine Liste von Add-ons. Jedes Add-on hat eine Beschriftung, einen Feldtyp, optional eine Pflicht-Kennzeichnung und optional einen Preis.

* <strong>Feldtypen</strong> — einfacher Text, ein Kontrollkästchen oder ein Auswahl-Dropdown.
* <strong>Textgrenzen</strong> — lege eine minimale und maximale Zeichenanzahl für Text-Produktoptionen fest.
* <strong>Preisdifferenzen</strong> — gib einem Add-on (oder jeder Auswahloption) einen Preis; der Betrag wird automatisch zur Zeilensumme im Warenkorb hinzugefügt.
* <strong>Kostenlos oder kostenpflichtig</strong> — belasse den Preis bei null für kostenlose Optionen wie eine personalisierte Nachricht.
* <strong>Anzeige in Warenkorb und Bestellung</strong> — die Auswahl der Kundschaft erscheint im Warenkorb, an der Kasse und in der Bestellung.
* <strong>Anzeigeeinstellungen</strong> — wähle die Gruppenüberschrift, blende Optionspreise ein oder aus, schalte das Sternchen des Pflichtfelds um und fasse die Optionen in einer umrandeten Karte zusammen, alles auf der Seite mit den Add-On-Einstellungen.

Add-on-Definitionen werden als Standard-Produktmeta gespeichert, ohne eigene Datenbanktabellen, sodass das Plugin selbst klein und schnell bleibt.

Die Einstellungen findest du unter <strong>WooCommerce → Add-Ons</strong>. Beim Entfernen des Plugins werden seine eigenen Optionen bereinigt; deine produktspezifischen Definitionen bleiben als Produktmeta erhalten, sodass sie bei einer Neuinstallation wiederhergestellt werden.

Der Code wird quelloffen unter https://github.com/wppoland/plogins-addons entwickelt – dort kannst du einen Fehler melden oder einen Feldtyp vorschlagen, den du dir wünschst.

== Installation ==

1. Lade das Plugin nach `/wp-content/plugins/plogins-addons` hoch oder installiere es über Plugins → Installieren.
2. Aktiviere es. WooCommerce muss aktiv sein.
3. Bearbeite ein Produkt, öffne den Tab <strong>Add-Ons</strong> im Produktdaten-Bereich und füge deine Optionen hinzu.

== Frequently Asked Questions ==

= Documentation and links =

* <strong>Dokumentation</strong> - https://plogins.com/de/plogins-addons/docs/
* <strong>Plugin-Seite</strong> - https://plogins.com/de/plogins-addons/
* <strong>Quellcode</strong> - https://github.com/wppoland/plogins-addons
* <strong>Fehlerberichte und Funktionswünsche</strong> - https://github.com/wppoland/plogins-addons/issues


= Does it require WooCommerce? =

Ja. WooCommerce muss installiert und aktiv sein.

= Where do customers see the options? =

Auf der einzelnen Produktseite, direkt über dem Button „In den Warenkorb“. Deine Auswahl erscheint dann im Warenkorb, an der Kasse und in der Bestellung.

= What field types are included? =

Die kostenlose Version umfasst Textfelder, Kontrollkästchen und Auswahl-Dropdowns. Jedes Feld kann kostenlos sein oder der Warenkorbzeile einen Preis hinzufügen.

= Can a product option change the price? =

Ja. Füge einen Preis zur Zeile selbst oder zu einzelnen Auswahloptionen hinzu, und das Plugin fügt diesen Betrag der WooCommerce-Warenkorbzeile hinzu.

= Can I make a product option required? =

Ja. Aktiviere das Kontrollkästchen „Erforderlich“ für eine Option, und das Produkt kann erst in den Warenkorb gelegt werden, wenn der Käufer sie ausgefüllt hat.

= Can I limit text option length? =

Ja. Text-Add-ons können minimale und maximale Zeichengrenzen haben. Der Shop zeigt einen Live-Zähler und der Server prüft dieselben Grenzen, bevor das Produkt in den Warenkorb gelegt wird.

= Does it create custom database tables? =

Nein. Add-on-Definitionen werden als Produktmeta gespeichert.

= Does it support file uploads or conditional logic? =

Das sind PRO-Funktionen. Add-Ons FREE konzentriert sich auf schnelle Produktoptionen: Text, Kontrollkästchen und Auswahl-Dropdowns.


= Does this plugin work on WordPress Multisite? =

Ja. Dieses Plugin ist mit WordPress Multisite kompatibel. Aktiviere es netzwerkweit oder auf einzelnen Websites; jede Website behält ihre eigenen Einstellungen und Daten.

== Screenshots ==

1. Im Shop.
2. Einstellungen im WordPress-Adminbereich.
3. Auf einem mobilen Gerät.
== External Services ==

Add-Ons stellt keine Verbindung zu externen Diensten her. Es sendet keine Daten von deiner Website und lädt keine externen Skripte, Schriftarten oder Tracker – sein Admin- und Shop-CSS/JS wird aus dem Plugin-Ordner auf deinem eigenen Server ausgeliefert. Deine Add-on-Definitionen werden als Produktmeta (`_addons_definitions`) gespeichert und die Anzeigeeinstellungen in einer einzigen Option (`addons_settings`), alles in deiner WordPress-Datenbank.

== Translations ==

Plogins Add-Ons enthält polnische, deutsche und spanische Übersetzungen für die Plugin-Oberfläche. Die Textdomain ist `plogins-addons`, sodass Sprachpakete von WordPress.org diese mitgelieferten Übersetzungen ebenfalls überschreiben oder erweitern können.

== Changelog ==

= 1.0.2 =
* Mitgelieferte polnische, deutsche und spanische Übersetzungen für die Plugin-Oberfläche hinzugefügt.

= 1.0.1 =
* Erste stabile Version.

= 0.3.1 =
* In Plogins Add-Ons für WooCommerce umbenannt, für einen eindeutigeren Plugin-Namen.

= 0.3.0 =
* Minimale und maximale Zeichengrenzen für Text-Add-ons hinzugefügt, mit Zählern im Shop und serverseitiger Prüfung.

= 0.2.0 =
* Anpassbare Gruppenüberschrift hinzugefügt, die über den Add-on-Feldern auf der Produktseite angezeigt wird.
* Anzeigeeinstellungen hinzugefügt: Optionspreise ein-/ausblenden, das Sternchen des Pflichtfelds umschalten und ein optionaler umrandeter Kartenstil.
* Deinstallationsroutine hinzugefügt, die die eigenen Optionen des Plugins entfernt (Produktdefinitionen bleiben als Produktmeta erhalten).

= 0.1.0 =
* Erstveröffentlichung.
