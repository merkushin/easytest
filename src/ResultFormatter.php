<?php declare(strict_types=1);

namespace EasyTest;

class ResultFormatter {

    public function format(int $passed, array $failures, array $errors): string {
        $result = '';
        if (count($failures) === 0 && count($errors) === 0) {
            $result .= "All tests passed.\n";
            return $result;
        } 

        $result .= "\nSome tests failed.\n";

        if (count($failures)) {
            $result .= "Failed tests:\n";
            foreach ($failures as  $i => $fail) {
                $result .= " - [" . ($i + 1) . "] " . $fail->getMessage() . "\n";
                $result .= $fail->getTraceAsString() . "\n\n";
            }
        }
        if (count($errors)) {
            $result .= "Errors:\n";
            foreach ($errors as  $i => $error) {
                $result .= " - [" . ($i + 1) . "] " . $error->getMessage() . "\n";
                $result .= $error->getTraceAsString() . "\n\n";
            }
        }

        return $result;
    }
}
