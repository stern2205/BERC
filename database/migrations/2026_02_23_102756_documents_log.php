<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('research_applications', function (Blueprint $table) {
            // Basic Requirements
            $table->string('doc_letter_request')->nullable();
            $table->string('doc_endorsement_letter')->nullable();
            $table->string('doc_full_proposal')->nullable();
            $table->string('doc_technical_review_approval')->nullable(); // To store the technical review document from reviewers

            // Informed Consent Form & Checkboxes (English/Filipino)
            $table->string('doc_informed_consent_english')->nullable(); //To store english version of the informed consent form
            $table->string('doc_informed_consent_filipino')->nullable(); // To store filipino version of the informed consent form

            // Manuscript
            $table->string('doc_manuscript')->nullable();
        });
    }

    public function down()
    {
        Schema::table('research_applications', function (Blueprint $table) {
            $table->dropColumn([
                'doc_letter_request',
                'doc_endorsement_letter',
                'doc_full_proposal',
                'doc_informed_consent',
                'icf_language',
                'doc_questionnaire',
                'doc_data_collection',
                'doc_manuscript',
                'doc_others',
                'doc_others_specify'
            ]);
        });
    }
};
