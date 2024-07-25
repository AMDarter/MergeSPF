<?php

class MergeSPF
{
    /**
     * Merge two SPF records into one.
     * 
     * @param string $spfRecord1 The first SPF record.
     * @param string $spfRecord2 The second SPF record.
     * @param string $default The default SPF record to return if the merge fails. Default: "v=spf1 -all".
     * @return string The merged SPF record or the default SPF record if the merge fails.
     */
    public static function merge(string $spfRecord1, string $spfRecord2, string $default = "v=spf1 -all"): string
    {
        try {
            // Trim whitespace.
            $spfRecord1 = trim($spfRecord1);
            $spfRecord2 = trim($spfRecord2);

            // Trim quotations
            $spfRecord1 = trim($spfRecord1, '"');
            $spfRecord2 = trim($spfRecord2, '"');

            // Validate
            if (strpos($spfRecord1, 'v=spf1') === false) {
                $spfRecord1 = '';
            }
            if (strpos($spfRecord2, 'v=spf1') === false) {
                $spfRecord2 = '';
            }

            // Check if empty.
            if (empty($spfRecord1) && empty($spfRecord2)) {
                return $default; // Default if both SPF records are empty
            }
            if (empty($spfRecord1) && !empty($spfRecord2)) {
                return $spfRecord2;
            }
            if (!empty($spfRecord1) && empty($spfRecord2)) {
                return $spfRecord1;
            }

            // Check if identical.
            if ($spfRecord1 === $spfRecord2) {
                return $spfRecord1;
            }

            // Define the priority for SPF qualifiers.
            $priority = ['-' => 1, '~' => 2, '?' => 3, '+' => 4];
            // Split the SPF records into their individual mechanisms.
            $mechanisms1 = array_map('trim', explode(' ', strtolower($spfRecord1)));
            $mechanisms2 = array_map('trim', explode(' ', strtolower($spfRecord2)));
            // Store the final mechanisms and modifiers.
            $allMechanisms = [];
            $allModifiers = [];
            // Default the all qualifier to "-all" for now.
            $allQualifier = '-all';
            // Iterate through each mechanism from both SPF records
            foreach (array_merge($mechanisms1, $mechanisms2) as $mechanism) {
                // Skip the SPF version string
                if ($mechanism === 'v=spf1') {
                    continue;
                }
                // Check for all qualifiers and prioritize them accordingly.
                if (in_array($mechanism, ['-all', '~all', '?all', '+all'])) {
                    if ($priority[$mechanism[0]] < $priority[$allQualifier[0]]) {
                        $allQualifier = $mechanism;
                    }
                    continue;
                }
                // Capture modifiers (redirect and exp) for later use
                if (strpos($mechanism, 'redirect=') !== false || strpos($mechanism, 'exp=') !== false) {
                    $allModifiers[] = $mechanism;
                    continue;
                }
                // Ensure every mechanism has a qualifier
                if (!in_array($mechanism[0], ['+', '-', '~', '?'])) {
                    $mechanism = '+' . $mechanism;
                }
                // Store or update the mechanism based on priority
                $key = substr($mechanism, 1);
                if (!isset($allMechanisms[$key])) {
                    $allMechanisms[$key] = $mechanism;
                } elseif ($priority[$mechanism[0]] < $priority[$allMechanisms[$key][0]]) {
                    $allMechanisms[$key] = $mechanism;
                }
            }
            // Remove the "+" qualifier for brevity
            foreach ($allMechanisms as $key => $mechanism) {
                if ($mechanism[0] === '+') {
                    $allMechanisms[$key] = substr($mechanism, 1);
                }
            }
            // Ensure the merged SPF record doesn't have too many DNS lookups
            if (count($allMechanisms) > 10) {
                return $default;
            }
            // Construct the merged SPF record
            $mergedRecord = 'v=spf1 ' . implode(' ', $allMechanisms) . ' ' . $allQualifier;
            // Add unique modifiers at the end of the SPF record.
            if (!empty($allModifiers)) {
                $mergedRecord .= ' ' . implode(' ', array_unique($allModifiers));
            }
            // Ensure the merged SPF record adheres to SPF specifications
            if (strlen($mergedRecord) > 255) {
                return $default;
            }
            // Remove any extra spaces for consistency
            return preg_replace('/\s+/', ' ', $mergedRecord);
        } catch (\Exception $e) {
            return $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }
}
