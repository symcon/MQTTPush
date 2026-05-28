# MQTT Push Instances

Dieses Modul sendet Messdaten von Geräte-Instanzen zyklisch als JSON an einen MQTT-Server.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanz in IP-Symcon](#4-einrichten-der-instanz-in-ip-symcon)
5. [Übertragungsformat](#5-übertragungsformat)
6. [Statusvariablen und Profile](#6-statusvariablen-und-profile)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

- Zyklischer Versand per konfigurierbarem Intervall.
- Versand per Button "Alles senden" in der Konfiguration.
- Rekursive Suche ab einer Basis-Kategorie/Instanz.
- Es werden nur Geräte-Instanzen (ModuleType Device) berücksichtigt.
- Pro Geräte-Instanz werden nur Variablen mit gesetztem Ident gesendet.

### 2. Voraussetzungen

- IP-Symcon ab Version 9.0
- Aktive MQTT Server Splitter Instanz als übergeordnete Instanz

### 3. Software-Installation

- Über den Module Store das Modul MQTT Push installieren.
- Alternativ über Module Control die Repository-URL hinzufügen.

### 4. Einrichten der Instanz in IP-Symcon

Unter Instanz hinzufügen kann das Modul MQTT Push Instances über den Filter gefunden werden.

Konfigurationsparameter:

Name      | Beschreibung
--------- | ---------------
BaseID    | Startobjekt, unterhalb dessen nach Geräte-Instanzen gesucht wird
BaseTopic | Optionales MQTT-Topic-Präfix
Interval  | Sendeintervall in Minuten

Aktionen:

Name            | Beschreibung
--------------- | --------------
Send everything | Sofortiger Versand aller Daten als Snapshot

### 5. Übertragungsformat

Topic-Aufbau:

- BaseTopic plus Pfad der Geräte-Instanz im Objektbaum

Hinweis:

- Falls BaseTopic leer ist, wird nur der Objektpfad verwendet.
- Ein fehlender Slash am Ende von BaseTopic wird automatisch ergänzt.

Payload (JSON):

- id: Geräte-ID der Instanz in Symcon
- timestamp: UTC-Zeitstempel zum Versandzeitpunkt
- payload: Objekt mit allen Variablen (nur mit Ident)

Beispielstruktur:

```json
{
       "id": "123456",
       "timestamp": "2026-05-28T10:15:30.0Z",
       "payload": {
              "Energy": {
                     "name": "Energie",
                     "value": 4711.25,
                     "unit": "kWh",
                     "timestamp": "2026-05-28T10:14:58.0Z"
              }
       }
}
```

Sonderfall M-Bus:

- Wenn der Geräte-Name dem Muster M-Bus ... (12345, XX, ...) entspricht, wird die extrahierte Nummer als id genutzt.

### 6. Statusvariablen und Profile

Das Modul legt keine eigenen Statusvariablen oder Profile an.

### 7. PHP-Befehlsreferenz

`void MQP_SendSnapshot(integer $InstanzID);`

Startet einen sofortigen Komplettversand unabhängig vom Timer.

Beispiel:

`MQP_SendSnapshot(12345);`