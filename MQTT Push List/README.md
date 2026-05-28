# MQTT Push List

Überträgt ausgewählte Zählerwerte bei Änderung und als Snapshot auf MQTT.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanz in IP-Symcon](#4-einrichten-der-instanz-in-ip-symcon)
5. [Übertragungsformat](#5-übertragungsformat)
6. [Statusvariablen und Profile](#6-statusvariablen-und-profile)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

- Jede konfigurierte Zähler-Variable wird bei Wertänderung versendet.
- Manueller Komplettversand per Button Alle Werte senden.
- Tabellarische Konfiguration mehrerer Zähler in einer Liste.
- Pro Zähler werden vier MQTT-Werte übertragen: Zählerstand, Maßeinheit, Zählernummer, Name.
- Fallback-Logik für Maßeinheit und Name, falls Felder in der Liste leer sind.

### 2. Voraussetzungen

- IP-Symcon ab Version 9.0
- Aktive MQTT Server Splitter Instanz als übergeordnete Instanz

### 3. Software-Installation

- Über den Module Store das Modul MQTT Push installieren.
- Alternativ über Module Control die Repository-URL hinzufügen.

### 4. Einrichten der Instanz in IP-Symcon

Unter Instanz hinzufügen kann das Modul MQTT Push List über den Filter gefunden werden.

Konfigurationsparameter:

Name              | Beschreibung
----------------- | ---------------------------------------------------------------
Gebäudenummer     | Fester Teil des Topic-Präfixes
Zähler            | Variable, die als Zählerstand gesendet wird
Messstellennummer | Zweite Topic-Ebene pro Eintrag
Zählernummer      | Wird als eigener Topic-Wert gesendet
Name              | Optionaler Name. Wenn leer, wird der Variablenname verwendet
Messgröße         | Optional. Wenn leer, wird das Variablen-Suffix genutzt

Aktionen:

Name              | Beschreibung
----------------- | ------------------------------------------------------
Alle Werte senden | Sofortiger Versand aller konfigurierten Listeneinträge

Hinweise zur Liste:

- Ein Listeneintrag wird nur verarbeitet, wenn eine gültige Variable gewählt ist.
- Für den Versand müssen Gebäudenummer und Messstellennummer gesetzt sein.

### 5. Übertragungsformat

Topic-Aufbau:

- Gebäudenummer/Messstellennummer/Zählerstand
- Gebäudenummer/Messstellennummer/Maßeinheit
- Gebäudenummer/Messstellennummer/Zählernummer
- Gebäudenummer/Messstellennummer/Name

Payload je Topic:

- Zählerstand: Aktueller Rohwert aus GetValue der konfigurierten Variable
- Maßeinheit: Wert aus Spalte Messgröße, sonst Suffix aus Variablenprofil
- Zählernummer: Wert aus Spalte Zählernummer
- Name: Wert aus Spalte Name, sonst Variablenname

Beispiel:

```text
FM100/EMIS200/Zählerstand   => 4711.25
FM100/EMIS200/Maßeinheit    => kWh
FM100/EMIS200/Zählernummer  => Z-01
FM100/EMIS200/Name          => Hauptzähler Heizung
```

### 6. Statusvariablen und Profile

Das Modul legt keine eigenen Statusvariablen oder Profile an.

### PHP-Befehlsreferenz

`void MQP_SendSnapshot(integer $InstanzID);`

Sendet alle konfigurierten Listeneinträge sofort auf MQTT.

Beispiel:

`MQP_SendSnapshot(12345);`
