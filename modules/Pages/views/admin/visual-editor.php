<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<style>
    /* Full-bleed iframe — override admin content padding for this page */
    #vcms-content {
        padding: 0 !important;
        max-width: 100% !important;
        display: flex;
        flex-direction: column;
        height: 100vh;
        overflow: hidden;
    }
    .vcms-main {
        display: flex;
        flex-direction: column;
    }
    .ve-admin-topbar {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.6rem 1.25rem;
        background: #0D0D0D;
        border-bottom: 2px solid #C9A227;
        flex-shrink: 0;
    }
    .ve-admin-topbar label {
        color: rgba(255,255,255,0.45);
        font-size: 0.72rem;
        font-family: 'Barlow Condensed', sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        white-space: nowrap;
    }
    .ve-admin-topbar select {
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 4px;
        color: #E8E8E8;
        padding: 0.35rem 0.65rem;
        font-size: 0.85rem;
        cursor: pointer;
        outline: none;
    }
    .ve-admin-topbar select:focus {
        border-color: #C9A227;
    }
    .ve-admin-iframe {
        flex: 1;
        width: 100%;
        border: none;
        display: block;
        overflow: hidden;
    }
</style>

<div class="ve-admin-topbar">
    <label>Seite:</label>
    <select id="ve-page-select">
        <?php foreach ($pages as $page): ?>
        <option value="<?= e($page['slug']) ?>"
            <?= $page['slug'] === $currentSlug ? ' selected' : '' ?>>
            <?= e($page['title']) ?>
        </option>
        <?php endforeach ?>
    </select>
</div>

<?php
$homepageSlug = setting('homepage_slug', 'home');
function veUrl(string $slug, string $homepageSlug): string {
    return ($slug === $homepageSlug ? '/' : '/' . $slug) . '?ve_edit=1&ve_embedded=1';
}
?>
<iframe
    id="ve-iframe"
    class="ve-admin-iframe"
    src="<?= e(veUrl($currentSlug, $homepageSlug)) ?>"
></iframe>

<script>
var homepageSlug = '<?= e($homepageSlug) ?>';
document.getElementById('ve-page-select').addEventListener('change', function () {
    var slug = this.value;
    var url = (slug === homepageSlug ? '/' : '/' + slug) + '?ve_edit=1&ve_embedded=1';
    document.getElementById('ve-iframe').src = url;
});
</script>

<?php $this->endSection() ?>
