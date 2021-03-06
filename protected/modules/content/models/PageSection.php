<?
/** 
 * 
 * !Attributes - атрибуты БД
 * @property string           $id
 * @property string           $parent_id
 * @property string           $name
 * @property integer          $order
 * @property string           $date_create
 * 
 * !Accessors - Геттеры и сеттеры класа и его поведений
 * @property                  $commentsCount
 * @property                  $lastTopic
 * @property                  $forumUrl
 * @property                  $errorsFlatArray
 * @property                  $url
 * @property                  $updateUrl
 * @property                  $createUrl
 * @property                  $deleteUrl
 * 
 * !Relations - связи
 * @property PageSection      $parent
 * @property PageSection[]    $childs
 * @property int|null         $pages_count
 * @property PageSectionRel[] $pages_sections_rels
 * 
 * !Scopes - именованные группы условий, возвращают этот АР
 * @method   PageSection      ordered()
 * @method   PageSection      last()
 * 
 */

class PageSection extends ActiveRecord
{
    const PAGE_SIZE = 20;

    const ROOT_SECTION_ID_PAGES = 1;
    const ROOT_SECTION_ID_FORUM = 2;


    public function name()
    {
        return 'Раздел страниц';
    }


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return 'pages_sections';
    }


    public function forum()
    {
        $this->dbCriteria->compare('parent_id', self::ROOT_SECTION_ID_FORUM);
        return $this;
    }


    public function pages()
    {
        $this->dbCriteria->compare('parent_id', self::ROOT_SECTION_ID_PAGES);
        return $this;
    }


    public function rules()
    {
        return [
            [
                'name',
                'required'
            ],
            [
                'name',
                'length',
                'max' => 50
            ],
            [
                'name',
                'unique'
            ],
            [
                'parent_id',
                'numerical',
                'integerOnly' => true
            ]
        ];
    }


    public function relations()
    {
        return [
            'parent' => [
                self::BELONGS_TO,
                'PageSection',
                'parent_id'
            ],
            'childs' => [
                self::HAS_MANY,
                'PageSection',
                'parent_id'
            ],
            'pages_count' => [
                self::STAT,
                'PageSectionRel',
                'section_id',
            ],
            'pages_sections_rels' => [
                self::HAS_MANY,
                'PageSectionRel',
                'section_id'
            ],
            'last_topic' => array(
                self::HAS_ONE,
                'Page',
                array('page_id' => 'id'),
                'through'   => 'pages_sections_rels',
                'order'     => 'last_topic.date_create DESC',
                'condition' => 'last_topic.status = "' . Page::STATUS_PUBLISHED . '"'
            )
        ];
    }


    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id, true);
        $criteria->compare('parent_id', $this->parent_id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('order', $this->order, true);
        $criteria->compare('date_create', $this->date_create, true);

        return new ActiveDataProvider(get_class($this), [
            'criteria'   => $criteria,
            'pagination' => [
                'pageSize' => self::PAGE_SIZE
            ]
        ]);
    }


    public function getCommentsCount()
    {
        $comments    = Comment::model()->tableName();
        $section_rel = PageSectionRel::model()->tableName();

        $sql = "SELECT COUNT(*)
                       FROM {$comments}
                       WHERE model_id = 'Page' AND
                             object_id IN (
                                 SELECT page_id
                                        FROM
                                        {$section_rel}
                                        WHERE section_id = {$this->id}
                             )";

        return $this->dbConnection->createCommand($sql)->queryScalar();
    }

    public function getForumUrl()
    {
        return Yii::app()->createUrl('/content/forum/sectionTopics/', ['section_id' => $this->id]);
    }
}
