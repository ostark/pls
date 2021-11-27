<?php

namespace ostark\PackageLister\Package;

class PluginPackage implements \JsonSerializable
{

    public function __construct(
        public string    $name,
        public string    $description,
        public string    $handle,
        public string    $repository,
        public ?string   $testLibrary,
        public int       $monthlyDownloads,
        public int       $dependents,
        public int       $favers,
        public \DateTime $updated)
    {
        //
    }


    public function jsonSerialize()
    {
        $fields = (array) $this;
        $fields['updated'] = $this->updated->format('Y-m-d');

        return $fields;
    }
}
