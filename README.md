# Convert spaces indentation to tabs in PHP files
Very simple CLI too to batch convert spaces indentations to tabs in PHP files with inteligent space counts grouping.

```bash
user@dir: ./convert.tabs.php /path/to/target/folder
```

If file looks good (spaces are consistently), file is converted without backup.

Tool detect inconsistencies, like a one some odd spaces indentated row between even spaces. 
