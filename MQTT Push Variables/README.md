# MQTT Push Variables

Dieses Modul sendet Variablenwerte unterhalb einer Basisstruktur als MQTT-Nachrichten.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanz in IP-Symcon](#4-einrichten-der-instanz-in-ip-symcon)
5. [Übertragungsformat](#5-übertragungsformat)
6. [Statusvariablen und Profile](#6-statusvariablen-und-profile)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

- Rekursive Suche aller Variablen unterhalb von BaseID.
- Jede gefundene Variable wird bei Wertänderung versendet.
- Manueller Komplettversand per Button "Alles senden".
- Versand jedes Wertes als formatierter Variablenwert.

### 2. Voraussetzungen

- IP-Symcon ab Version 9.0
- Aktive MQTT Server Splitter Instanz als übergeordnete Instanz

### 3. Software-Installation

- Über den Module Store das Modul MQTT Push installieren.
- Alternativ über Module Control die Repository-URL hinzufügen.

### 4. Einrichten der Instanz in IP-Symcon

Unter Instanz hinzufügen kann das Modul MQTT Push Variables über den Filter gefunden werden.

Konfigurationsparameter:

Name      | Beschreibung
----------| --------------
BaseID    | Startobjekt, unterhalb dessen nach Variablen gesucht wird
BaseTopic | Optionales MQTT-Topic-Präfix

Aktionen:

Name         | Beschreibung
------------ | --------------
Alles senden | Sofortiger Versand aller gefundenen Variablen

### 5. Übertragungsformat

Topic-Aufbau:

- BaseTopic plus vollständiger Objektpfad inklusive Objekt-ID

Beispiel:

- Gebäude/Heizung/Vorlauf (12345)

Payload:

- Formatierter Variablenwert aus GetValueFormatted.

Hinweis:

- Ein fehlender Slash am Ende von BaseTopic wird automatisch ergänzt.

### 6. Statusvariablen und Profile

Das Modul legt keine eigenen Statusvariablen oder Profile an.

### 7. PHP-Befehlsreferenz

`void MQP_SendSnapshot(integer $InstanzID);`

Startet einen sofortigen Komplettversand aller Variablen unterhalb von BaseID.

Beispiel:

`MQP_SendSnapshot(12345);`