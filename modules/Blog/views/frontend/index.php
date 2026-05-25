<?php declare(strict_types=1); ?>
<?php $this->section('title') ?><?= t('blog.headline') ?><?php $this->endSection() ?>
<?php $this->section('content') ?>

<div class="vcms-container vcms-blog">
    <h1><?= t('blog.headline') ?></h1>

    <?php if (empty($posts)): ?>
        <p class="vcms-blog-empty"><?= t('blog.empty') ?></p>
    <?php else: ?>
    <div class="vcms-blog-grid">
        <?php foreach ($posts as $p): ?>
        <article class="vcms-blog-card">
            <?php if (!empty($p['cover_image'])): ?>
            <div class="vcms-blog-card__cover">
                <a href="/blog/<?= e($p['slug']) ?>">
                    <img src="<?= e($p['cover_image']) ?>" alt="<?= e(localized($p, 'title')) ?>" loading="lazy">
                </a>
            </div>
            <?php endif ?>
            <div class="vcms-blog-card__body">
                <h2 class="vcms-blog-card__title">
                    <a href="/blog/<?= e($p['slug']) ?>"><?= e(localized($p, 'title')) ?></a>
                </h2>
                <?php if (!empty($p['excerpt']) || !empty($p['excerpt_en'])): ?>
                <p class="vcms-blog-card__excerpt"><?= e(localized($p, 'excerpt')) ?></p>
                <?php endif ?>
                <span class="vcms-blog-card__date"><?= e(substr($p['published_at'] ?? $p['created_at'], 0, 10)) ?></span>
            </div>
        </article>
        <?php endforeach ?>
    </div>

    <?php if ($total > $perPage): ?>
    <div class="vcms-pagination">
        <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
        <a href="/blog?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor ?>
    </div>
    <?php endif ?>
    <?php endif ?>
</div>
<?php $this->endSection() ?>
