<?php

echo $this->extend('default');
echo $this->section('content');

if (! function_exists('anyIssueGoingToSection3')) {
    /*
     * If no error message, go to next section.
     *
     * @return array<string> List of issues.
     */
    function anyIssueGoingToSection3(array $patient): array
    {
        $listOfIssues = [];
        if (! currentSampleCode()) {
            $listOfIssues[] = 'No sample is selected';
        }
        if (! ($patient['uid'] ?? null)) {
            $listOfIssues[] = 'Patient is not found';
        }

        return $listOfIssues;
    }
}

?>

<?php echo view_cell('EhrFormInfoCell'); ?>

<h1 class="section-title">Section 2</h1>


<?php echo $this->endSection(); ?>
