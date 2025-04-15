<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div id="about-page" class="container backend-page">
    <div id="about" class="col-lg-8 offset-lg-2">

        <div class="text-center my-5 header-logo">
            <img src="<?= base_url('assets/img/logo_black.png') ?>" alt="@webSchedulr" class="mb-5" style="height: 100px;">

            <h6 class="text-primary">
            Simple scheduling for anything
            </h6>
        </div>

        <p class="mb-5">
            <?= lang('about_app_info') ?>
        </p>

        <div class="card mb-5">
            <div class="card-header">
                <h5 class="fw-light text-black-50 mb-0">
                    <?= lang('current_version') ?>
                </h5>
            </div>
            <div class="card-body">
                <strong>
                    <?= config('version') ?>
                </strong>
            </div>
        </div>

        <h4 class="fw-light text-black-50 mb-3">
            <?= lang('support') ?>
        </h4>

        <p>
            <?= lang('about_app_support') ?>
        </p>
        
        <div class="row mb-5">
            <div class="col-lg-6 mb-3">
                <a class="btn btn-outline-secondary d-block" href="https://webschedulr.co.za" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>
                    <?= lang('official_website') ?>
                </a>
            </div>

            <div class="col-lg-6 mb-3">
                <a class="btn btn-outline-secondary d-block"
                   href="https://frontend-za.atlassian.net/jira/servicedesk/projects/CS/settings/request-types/request-type/1/request-form" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>
                    <?= lang('support_group') ?>
                </a>
            </div>

            <div class="col-lg-6 mb-3">
                <a class="btn btn-outline-secondary d-block"
                   href="https://github.com/niloc95/webSchedulr-Reborn/issues" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>
                    <?= lang('project_issues') ?>
                </a>
            </div>        

            <div class="col-lg-6 mb-3">
                <a class="btn btn-outline-secondary d-block" href="https://webschedulr.co.za/contact" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>
                    White Label
                </a>
            </div>
        </div>

        <h4 class="fw-light text-black-50 mb-3">
            <?= lang('license') ?>
        </h4>

        <p>
            <?= lang('about_app_license') ?>
        </p>

        <div class="mb-5">
            <a class="btn btn-outline-secondary d-block w-50 m-auto" href="https://www.gnu.org/licenses/gpl-3.0.en.html"
               target="_blank">
                <i class="fas fa-external-link-alt me-2"></i>
                GPL-3.0
            </a>
        </div>
    </div>
</div>

<?php end_section('content'); ?>
