<?php

namespace Imagescroller\Infra;

class FakeRepository extends Repository
{
    private $options;

    public function options(array $options)
    {
        $this->options = $options;
    }

    public function saveGallery(string $gallery, string $contents): bool
    {
        if (isset($this->options["saveGallery"])) {
            return $this->options["saveGallery"];
        }
        return parent::saveGallery($gallery, $contents);
    }
}