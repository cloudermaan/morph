<?php
/**
 * @package Morph
 * @subpackage Property
 * @author Jonathan Moss <xirisr@gmail.com>
 * @copyright Jonathan Moss 2009
 */

/**
 * This property type represents a collection of many Morph_Objects.
 *
 * Each object contained within this collection is stored separately as it's own object,
 * independent of the parent object
 *
 * @package Morph
 * @subpackage Property
 */
class  Morph_Property_HasMany extends Morph_Property_Generic
{

    /**
     * @var boolean
     */
    protected $Loaded = false;

    /**
     * @var String
     */
    protected $Type;

    /**
     * @var array
     */
    protected $References;


    /**
     *
     * @param String $name
     * @param String $type
     * @return Morph_Property_HasMany
     */
    public function __construct($name, $type)
    {
        $this->Type = $type;
        $default = new Morph_Collection();
        $default->setPermissableType($type);
        parent::__construct($name, $default);
    }

    /**
     * (non-PHPdoc)
     * @see tao/classes/Morph/property/Morph_Property_Generic#__setRawValue()
     */
    public function __setRawValue($value)
    {
        $this->References = $value;
    }

    /**
     * (non-PHPdoc)
     * @see tao/classes/Morph/property/Morph_Property_Generic#__getRawValue()
     */
    public function __getRawValue()
    {
        if(count($this->Value) > 0){
            $refs = array();
            foreach ($this->Value as $object){
                if($object->state() != Morph_Enum::STATE_CLEAN){
                    Morph_Storage::instance()->save($object);
                }
                $refs[] = Morph_Utils::objectReference($object);
            }
            $this->References = $refs;
        }
        return $this->References;
    }

    /**
     * (non-PHPdoc)
     * @see tao/classes/Morph/property/Morph_Property_Generic#getValue()
     */
    public function getValue()
    {
        if($this->Loaded === false){
            $this->loadFromReferences();
        }
        return $this->Value;
    }

    /**
     * (non-PHPdoc)
     * @see tao/classes/Morph/property/Morph_Property_Generic#setValue()
     */
    public function setValue($value)
    {
        if($value instanceof Morph_Collection){
            $this->Value = $value;
            $this->References = array();
        }
    }


    /**
     * Loads the stored references
     * @return void
     */
    private function loadFromReferences()
    {
        $ids = array();
        if (count($this->References) > 0) {
            foreach ($this->References as $reference){
                $ids[] = $reference['$id'];
            }

            $query = new Morph_Query();
            $query->property('_id')->in($ids);

            $object = new $this->Type;

            //@todo this could get nasty with large collections!
            $this->Value = Morph_Storage::instance()->findByQuery($object, $query)->toCollection();
        } else {
            $this->Value = new Morph_Collection();
        }
        $this->Loaded = true;
    }
}