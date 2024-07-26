# MergeSPF

`MergeSPF` is a PHP class designed to merge two SPF (Sender Policy Framework) records into a single, consolidated SPF record. This is useful for combining SPF records from multiple sources into one that adheres to SPF specifications.

## Features

- **Merge Two SPF Records**: Combines two SPF records into one.
- **Default SPF Record**: Returns a default SPF record if the merge fails or if both input records are invalid.
- **SPF Record Validation**: Ensures that only valid SPF records are processed.
- **Handles SPF Qualifiers**: Prioritizes SPF qualifiers and handles mechanisms and modifiers accordingly.
- **Ensures SPF Specifications**: Adheres to SPF record specifications, including DNS lookups and length constraints.

## Usage

To use the `MergeSPF` class, follow these steps:

1. **Include the PHP File**: Ensure that the PHP file containing the `MergeSPF` class is included in your project.

    ```php
    use AMDarter\MergeSPF;
    ```

2. **Call the `merge` Method**: Use the static `merge` method to combine two SPF records.

    ```php
    $spfRecord1 = 'v=spf1 ip4:192.0.2.0/24 -all';
    $spfRecord2 = 'v=spf1 ip4:203.0.113.0/24 ~all';

    $mergedSpf = MergeSPF::merge($spfRecord1, $spfRecord2);

    echo $mergedSpf;
    ```

    The `merge` method parameters are:

    - `$spfRecord1` (string): The first SPF record.
    - `$spfRecord2` (string): The second SPF record.
    - `$default` (string, optional): The default SPF record to return if the merge fails. Defaults to `"v=spf1 -all"`.

3. **Handle Exceptions**: The method will return the default SPF record if an exception or error occurs during the merge process.

## Requirements
PHP 7.0 or later.

## License
This project is licensed under the MIT License. See the LICENSE file for details.

## Contributing
Contributions are welcome! Please open an issue or submit a pull request.