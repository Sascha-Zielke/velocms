<?php declare(strict_types=1); ?>
<?php $this->section('title') ?><?= e(localized($post, 'meta_title', 'velocms_blog_posts') ?: localized($post, 'title', 'velocms_blog_posts')) ?><?php $this->endSection() ?>
<?php $this->section('meta_description') ?><?= e($post['meta_description'] ?? '') ?><?php $this->endSection() ?>
<?php if (!empty($post['cover_image'])): ?><?php $this->section('og_image') ?><?= e($post['cover_image']) ?><?php $this->endSection() ?><?php endif ?>
<?php $this->section('content') ?>

<article class="vcms-blog-post">
    <h1><?= e(localized($post, 'title', 'velocms_blog_posts')) ?></h1>
    <div class="vcms-blog-post__meta">
        <?= e(substr($post['published_at'] ?? $post['created_at'], 0, 10)) ?>
    </div>
    <?php if (!empty($post['cover_image'])): ?>
    <img src="<?= e($post['cover_image']) ?>" alt="<?= e(localized($post, 'title', 'velocms_blog_posts')) ?>"
         class="vcms-blog-post__cover" loading="eager">
    <?php endif ?>
    <div class="vcms-blog-post__body">
        <?= safe_html(localized($post, 'content', 'velocms_blog_posts')) ?>
    </div>
    <p style="margin-top:48px"><a href="/blog">← <?= t('blog.back') ?></a></p>
</article>
<?php $this->endSection() ?>
