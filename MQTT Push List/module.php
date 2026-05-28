<?php

declare(strict_types=1);
class MQTTPushList extends IPSModuleStrict
{
    public function Create(): void
    {
        $this->RegisterPropertyString('BuildingNumber', '');
        $this->RegisterPropertyString('Meters', '[]');
    }

    public function ApplyChanges(): void
    {
        // Never delete this line!
        parent::ApplyChanges();

        $this->UpdateSubscription();
        $this->UpdateReferences();
    }

    public function ReceiveData($JSONString): string
    {
        return '';
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data): void
    {
        if ($Message !== VM_UPDATE) {
            return;
        }
        // Only send changes (HasDiff = true)
        if (!$Data[1]) {
            return;
        }

        $entries = $this->GetEntries();
        foreach ($entries as $entry) {
            if ($entry['VariableID'] === $SenderID) {
                $this->SendEntry($entry);
            }
        }
    }

    public function UpdateSubscription(): void
    {
        // Delete all registrations and re-add selected variables.
        foreach ($this->GetMessageList() as $senderID => $messages) {
            foreach ($messages as $message) {
                $this->UnregisterMessage($senderID, $message);
            }
        }

        $entries = $this->GetEntries();
        foreach ($entries as $entry) {
            if (IPS_VariableExists($entry['VariableID'])) {
                $this->RegisterMessage($entry['VariableID'], VM_UPDATE);
            }
        }
    }

    public function UpdateReferences(): void
    {
        // Delete all registrations and re-add selected variables.
        foreach ($this->GetReferenceList() as $senderID => $messages) {
            foreach ($messages as $message) {
                $this->UnregisterReference($senderID, $message);
            }
        }
    
        $entries = $this->GetEntries();
        foreach ($entries as $entry) {
            if (IPS_VariableExists($entry['VariableID'])) {
                $this->RegisterReference($entry['VariableID']);
            }
        }
    }

    public function SendSnapshot(): void
    {
        $entries = $this->GetEntries();
        foreach ($entries as $entry) {
            $this->SendEntry($entry);
        }
    }

    private function GetEntries(): array
    {
        $raw = json_decode($this->ReadPropertyString('Meters'), true);
        if (!is_array($raw)) {
            return [];
        }

        $entries = [];
        foreach ($raw as $item) {
            if (!is_array($item)) {
                continue;
            }

            $entries[] = [
                'VariableID' => (int) ($item['VariableID'] ?? 0),
                'MeteringPointNumber' => trim((string) ($item['MeteringPointNumber'] ?? '')),
                'MeterNumber' => trim((string) ($item['MeterNumber'] ?? '')),
                'Name' => trim((string) ($item['Name'] ?? '')),
                'Unit' => trim((string) ($item['Unit'] ?? '')),
            ];
        }

        return $entries;
    }

    private function SendEntry(array $entry): void
    {
        $variableID = $entry['VariableID'];
        if ($variableID <= 0 || !IPS_VariableExists($variableID)) {
            return;
        }

        $buildingNumber = trim($this->ReadPropertyString('BuildingNumber'));
        $meteringPointNumber = trim($entry['MeteringPointNumber']);
        if ($buildingNumber === '' || $meteringPointNumber === '') {
            return;
        }

        $topicBase = $buildingNumber . '/' . $meteringPointNumber;

        $value = GetValue($variableID);
        $unit = $entry['Unit'];
        if ($unit === '') {
            $unit = $this->GetVariableSuffix($variableID);
        }

        $name = $entry['Name'];
        if ($name === '') {
            $name = IPS_GetName($variableID);
        }

        $this->Send($topicBase . '/' . $this->Translate('MeterValue'), $this->ValueToString($value));
        $this->Send($topicBase . '/' . $this->Translate('Unit'), $unit);
        $this->Send($topicBase . '/' . $this->Translate('MeterNumber'), $entry['MeterNumber']);
        $this->Send($topicBase . '/' . $this->Translate('Name'), $name);
    }

    private function GetVariableSuffix(int $variableID): string
    {
        $variable = IPS_GetVariable($variableID);
        $profileName = $variable['VariableCustomProfile'] !== ''
            ? $variable['VariableCustomProfile']
            : $variable['VariableProfile'];

        if ($profileName === '' || !IPS_VariableProfileExists($profileName)) {
            return '';
        }

        $profile = IPS_GetVariableProfile($profileName);
        return trim((string) $profile['Suffix']);
    }

    private function ValueToString($value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value);
    }

    private function Send(string $topic, string $payload): void
    {
        $packet = [];
        $packet['DataID'] = '{043EA491-0325-4ADD-8FC2-A30C8EEB4D3F}';
        $packet['PacketType'] = 3;
        $packet['QualityOfService'] = 0;
        $packet['Retain'] = false;
        $packet['Topic'] = $topic;
        $packet['Payload'] = bin2hex($payload);

        $this->SendDebug($topic, $payload, 0);
        $this->SendDataToParent(json_encode($packet));
    }
}
