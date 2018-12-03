<?php

namespace Base;

use \Cart as ChildCart;
use \CartQuery as ChildCartQuery;
use \Mailingaddress as ChildMailingaddress;
use \MailingaddressQuery as ChildMailingaddressQuery;
use \Transactions as ChildTransactions;
use \TransactionsQuery as ChildTransactionsQuery;
use \Users as ChildUsers;
use \UsersQuery as ChildUsersQuery;
use \DateTime;
use \Exception;
use \PDO;
use Map\CartTableMap;
use Map\MailingaddressTableMap;
use Map\TransactionsTableMap;
use Map\UsersTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

/**
 * Base class that represents a row from the 'Users' table.
 *
 *
 *
 * @package    propel.generator..Base
 */
abstract class Users implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\UsersTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     *
     * @var        int
     */
    protected $id;

    /**
     * The value for the first_name field.
     *
     * @var        string
     */
    protected $first_name;

    /**
     * The value for the last_name field.
     *
     * @var        string
     */
    protected $last_name;

    /**
     * The value for the email field.
     *
     * @var        string
     */
    protected $email;

    /**
     * The value for the password_hash field.
     *
     * @var        string
     */
    protected $password_hash;

    /**
     * The value for the paypal_email field.
     *
     * @var        string
     */
    protected $paypal_email;

    /**
     * The value for the join_date field.
     *
     * @var        DateTime
     */
    protected $join_date;

    /**
     * @var        ObjectCollection|ChildCart[] Collection to store aggregation of ChildCart objects.
     */
    protected $collCarts;
    protected $collCartsPartial;

    /**
     * @var        ObjectCollection|ChildMailingaddress[] Collection to store aggregation of ChildMailingaddress objects.
     */
    protected $collMailingaddresses;
    protected $collMailingaddressesPartial;

    /**
     * @var        ObjectCollection|ChildTransactions[] Collection to store aggregation of ChildTransactions objects.
     */
    protected $collTransactionss;
    protected $collTransactionssPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildCart[]
     */
    protected $cartsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildMailingaddress[]
     */
    protected $mailingaddressesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildTransactions[]
     */
    protected $transactionssScheduledForDeletion = null;

    /**
     * Initializes internal state of Base\Users object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Users</code> instance.  If
     * <code>obj</code> is an instance of <code>Users</code>, delegates to
     * <code>equals(Users)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|Users The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        $cls = new \ReflectionClass($this);
        $propertyNames = [];
        $serializableProperties = array_diff($cls->getProperties(), $cls->getProperties(\ReflectionProperty::IS_STATIC));

        foreach($serializableProperties as $property) {
            $propertyNames[] = $property->getName();
        }

        return $propertyNames;
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [first_name] column value.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Get the [last_name] column value.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Get the [email] column value.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the [password_hash] column value.
     *
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->password_hash;
    }

    /**
     * Get the [paypal_email] column value.
     *
     * @return string
     */
    public function getPaypalEmail()
    {
        return $this->paypal_email;
    }

    /**
     * Get the [optionally formatted] temporal [join_date] column value.
     *
     *
     * @param      string|null $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getJoinDate($format = NULL)
    {
        if ($format === null) {
            return $this->join_date;
        } else {
            return $this->join_date instanceof \DateTimeInterface ? $this->join_date->format($format) : null;
        }
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\Users The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[UsersTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [first_name] column.
     *
     * @param string $v new value
     * @return $this|\Users The current object (for fluent API support)
     */
    public function setFirstName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->first_name !== $v) {
            $this->first_name = $v;
            $this->modifiedColumns[UsersTableMap::COL_FIRST_NAME] = true;
        }

        return $this;
    } // setFirstName()

    /**
     * Set the value of [last_name] column.
     *
     * @param string $v new value
     * @return $this|\Users The current object (for fluent API support)
     */
    public function setLastName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->last_name !== $v) {
            $this->last_name = $v;
            $this->modifiedColumns[UsersTableMap::COL_LAST_NAME] = true;
        }

        return $this;
    } // setLastName()

    /**
     * Set the value of [email] column.
     *
     * @param string $v new value
     * @return $this|\Users The current object (for fluent API support)
     */
    public function setEmail($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[UsersTableMap::COL_EMAIL] = true;
        }

        return $this;
    } // setEmail()

    /**
     * Set the value of [password_hash] column.
     *
     * @param string $v new value
     * @return $this|\Users The current object (for fluent API support)
     */
    public function setPasswordHash($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->password_hash !== $v) {
            $this->password_hash = $v;
            $this->modifiedColumns[UsersTableMap::COL_PASSWORD_HASH] = true;
        }

        return $this;
    } // setPasswordHash()

    /**
     * Set the value of [paypal_email] column.
     *
     * @param string $v new value
     * @return $this|\Users The current object (for fluent API support)
     */
    public function setPaypalEmail($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->paypal_email !== $v) {
            $this->paypal_email = $v;
            $this->modifiedColumns[UsersTableMap::COL_PAYPAL_EMAIL] = true;
        }

        return $this;
    } // setPaypalEmail()

    /**
     * Sets the value of [join_date] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\Users The current object (for fluent API support)
     */
    public function setJoinDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->join_date !== null || $dt !== null) {
            if ($this->join_date === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->join_date->format("Y-m-d H:i:s.u")) {
                $this->join_date = $dt === null ? null : clone $dt;
                $this->modifiedColumns[UsersTableMap::COL_JOIN_DATE] = true;
            }
        } // if either are not null

        return $this;
    } // setJoinDate()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : UsersTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : UsersTableMap::translateFieldName('FirstName', TableMap::TYPE_PHPNAME, $indexType)];
            $this->first_name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : UsersTableMap::translateFieldName('LastName', TableMap::TYPE_PHPNAME, $indexType)];
            $this->last_name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : UsersTableMap::translateFieldName('Email', TableMap::TYPE_PHPNAME, $indexType)];
            $this->email = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : UsersTableMap::translateFieldName('PasswordHash', TableMap::TYPE_PHPNAME, $indexType)];
            $this->password_hash = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : UsersTableMap::translateFieldName('PaypalEmail', TableMap::TYPE_PHPNAME, $indexType)];
            $this->paypal_email = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : UsersTableMap::translateFieldName('JoinDate', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->join_date = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 7; // 7 = UsersTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Users'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(UsersTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildUsersQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collCarts = null;

            $this->collMailingaddresses = null;

            $this->collTransactionss = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Users::setDeleted()
     * @see Users::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(UsersTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildUsersQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($this->alreadyInSave) {
            return 0;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(UsersTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $ret = $this->preSave($con);
            $isInsert = $this->isNew();
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                UsersTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                    $affectedRows += 1;
                } else {
                    $affectedRows += $this->doUpdate($con);
                }
                $this->resetModified();
            }

            if ($this->cartsScheduledForDeletion !== null) {
                if (!$this->cartsScheduledForDeletion->isEmpty()) {
                    \CartQuery::create()
                        ->filterByPrimaryKeys($this->cartsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->cartsScheduledForDeletion = null;
                }
            }

            if ($this->collCarts !== null) {
                foreach ($this->collCarts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->mailingaddressesScheduledForDeletion !== null) {
                if (!$this->mailingaddressesScheduledForDeletion->isEmpty()) {
                    \MailingaddressQuery::create()
                        ->filterByPrimaryKeys($this->mailingaddressesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->mailingaddressesScheduledForDeletion = null;
                }
            }

            if ($this->collMailingaddresses !== null) {
                foreach ($this->collMailingaddresses as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->transactionssScheduledForDeletion !== null) {
                if (!$this->transactionssScheduledForDeletion->isEmpty()) {
                    \TransactionsQuery::create()
                        ->filterByPrimaryKeys($this->transactionssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->transactionssScheduledForDeletion = null;
                }
            }

            if ($this->collTransactionss !== null) {
                foreach ($this->collTransactionss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[UsersTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . UsersTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(UsersTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(UsersTableMap::COL_FIRST_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'first_name';
        }
        if ($this->isColumnModified(UsersTableMap::COL_LAST_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'last_name';
        }
        if ($this->isColumnModified(UsersTableMap::COL_EMAIL)) {
            $modifiedColumns[':p' . $index++]  = 'email';
        }
        if ($this->isColumnModified(UsersTableMap::COL_PASSWORD_HASH)) {
            $modifiedColumns[':p' . $index++]  = 'password_hash';
        }
        if ($this->isColumnModified(UsersTableMap::COL_PAYPAL_EMAIL)) {
            $modifiedColumns[':p' . $index++]  = 'paypal_email';
        }
        if ($this->isColumnModified(UsersTableMap::COL_JOIN_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'join_date';
        }

        $sql = sprintf(
            'INSERT INTO Users (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'id':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'first_name':
                        $stmt->bindValue($identifier, $this->first_name, PDO::PARAM_STR);
                        break;
                    case 'last_name':
                        $stmt->bindValue($identifier, $this->last_name, PDO::PARAM_STR);
                        break;
                    case 'email':
                        $stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
                        break;
                    case 'password_hash':
                        $stmt->bindValue($identifier, $this->password_hash, PDO::PARAM_STR);
                        break;
                    case 'paypal_email':
                        $stmt->bindValue($identifier, $this->paypal_email, PDO::PARAM_STR);
                        break;
                    case 'join_date':
                        $stmt->bindValue($identifier, $this->join_date ? $this->join_date->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = UsersTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getFirstName();
                break;
            case 2:
                return $this->getLastName();
                break;
            case 3:
                return $this->getEmail();
                break;
            case 4:
                return $this->getPasswordHash();
                break;
            case 5:
                return $this->getPaypalEmail();
                break;
            case 6:
                return $this->getJoinDate();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {

        if (isset($alreadyDumpedObjects['Users'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Users'][$this->hashCode()] = true;
        $keys = UsersTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getFirstName(),
            $keys[2] => $this->getLastName(),
            $keys[3] => $this->getEmail(),
            $keys[4] => $this->getPasswordHash(),
            $keys[5] => $this->getPaypalEmail(),
            $keys[6] => $this->getJoinDate(),
        );
        if ($result[$keys[6]] instanceof \DateTimeInterface) {
            $result[$keys[6]] = $result[$keys[6]]->format('c');
        }

        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collCarts) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'carts';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'Carts';
                        break;
                    default:
                        $key = 'Carts';
                }

                $result[$key] = $this->collCarts->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collMailingaddresses) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'mailingaddresses';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'MailingAddresses';
                        break;
                    default:
                        $key = 'Mailingaddresses';
                }

                $result[$key] = $this->collMailingaddresses->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collTransactionss) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'transactionss';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'Transactionss';
                        break;
                    default:
                        $key = 'Transactionss';
                }

                $result[$key] = $this->collTransactionss->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string $name
     * @param  mixed  $value field value
     * @param  string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this|\Users
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = UsersTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Users
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setFirstName($value);
                break;
            case 2:
                $this->setLastName($value);
                break;
            case 3:
                $this->setEmail($value);
                break;
            case 4:
                $this->setPasswordHash($value);
                break;
            case 5:
                $this->setPaypalEmail($value);
                break;
            case 6:
                $this->setJoinDate($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = UsersTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setFirstName($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setLastName($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setEmail($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setPasswordHash($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setPaypalEmail($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setJoinDate($arr[$keys[6]]);
        }
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     * @param string $keyType The type of keys the array uses.
     *
     * @return $this|\Users The current object, for fluid interface
     */
    public function importFrom($parser, $data, $keyType = TableMap::TYPE_PHPNAME)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), $keyType);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(UsersTableMap::DATABASE_NAME);

        if ($this->isColumnModified(UsersTableMap::COL_ID)) {
            $criteria->add(UsersTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(UsersTableMap::COL_FIRST_NAME)) {
            $criteria->add(UsersTableMap::COL_FIRST_NAME, $this->first_name);
        }
        if ($this->isColumnModified(UsersTableMap::COL_LAST_NAME)) {
            $criteria->add(UsersTableMap::COL_LAST_NAME, $this->last_name);
        }
        if ($this->isColumnModified(UsersTableMap::COL_EMAIL)) {
            $criteria->add(UsersTableMap::COL_EMAIL, $this->email);
        }
        if ($this->isColumnModified(UsersTableMap::COL_PASSWORD_HASH)) {
            $criteria->add(UsersTableMap::COL_PASSWORD_HASH, $this->password_hash);
        }
        if ($this->isColumnModified(UsersTableMap::COL_PAYPAL_EMAIL)) {
            $criteria->add(UsersTableMap::COL_PAYPAL_EMAIL, $this->paypal_email);
        }
        if ($this->isColumnModified(UsersTableMap::COL_JOIN_DATE)) {
            $criteria->add(UsersTableMap::COL_JOIN_DATE, $this->join_date);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = ChildUsersQuery::create();
        $criteria->add(UsersTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Users (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setFirstName($this->getFirstName());
        $copyObj->setLastName($this->getLastName());
        $copyObj->setEmail($this->getEmail());
        $copyObj->setPasswordHash($this->getPasswordHash());
        $copyObj->setPaypalEmail($this->getPaypalEmail());
        $copyObj->setJoinDate($this->getJoinDate());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getCarts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCart($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getMailingaddresses() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addMailingaddress($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getTransactionss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTransactions($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param  boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \Users Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('Cart' == $relationName) {
            $this->initCarts();
            return;
        }
        if ('Mailingaddress' == $relationName) {
            $this->initMailingaddresses();
            return;
        }
        if ('Transactions' == $relationName) {
            $this->initTransactionss();
            return;
        }
    }

    /**
     * Clears out the collCarts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCarts()
     */
    public function clearCarts()
    {
        $this->collCarts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCarts collection loaded partially.
     */
    public function resetPartialCarts($v = true)
    {
        $this->collCartsPartial = $v;
    }

    /**
     * Initializes the collCarts collection.
     *
     * By default this just sets the collCarts collection to an empty array (like clearcollCarts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCarts($overrideExisting = true)
    {
        if (null !== $this->collCarts && !$overrideExisting) {
            return;
        }

        $collectionClassName = CartTableMap::getTableMap()->getCollectionClassName();

        $this->collCarts = new $collectionClassName;
        $this->collCarts->setModel('\Cart');
    }

    /**
     * Gets an array of ChildCart objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUsers is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildCart[] List of ChildCart objects
     * @throws PropelException
     */
    public function getCarts(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCartsPartial && !$this->isNew();
        if (null === $this->collCarts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCarts) {
                // return empty collection
                $this->initCarts();
            } else {
                $collCarts = ChildCartQuery::create(null, $criteria)
                    ->filterByUsers($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCartsPartial && count($collCarts)) {
                        $this->initCarts(false);

                        foreach ($collCarts as $obj) {
                            if (false == $this->collCarts->contains($obj)) {
                                $this->collCarts->append($obj);
                            }
                        }

                        $this->collCartsPartial = true;
                    }

                    return $collCarts;
                }

                if ($partial && $this->collCarts) {
                    foreach ($this->collCarts as $obj) {
                        if ($obj->isNew()) {
                            $collCarts[] = $obj;
                        }
                    }
                }

                $this->collCarts = $collCarts;
                $this->collCartsPartial = false;
            }
        }

        return $this->collCarts;
    }

    /**
     * Sets a collection of ChildCart objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $carts A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildUsers The current object (for fluent API support)
     */
    public function setCarts(Collection $carts, ConnectionInterface $con = null)
    {
        /** @var ChildCart[] $cartsToDelete */
        $cartsToDelete = $this->getCarts(new Criteria(), $con)->diff($carts);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->cartsScheduledForDeletion = clone $cartsToDelete;

        foreach ($cartsToDelete as $cartRemoved) {
            $cartRemoved->setUsers(null);
        }

        $this->collCarts = null;
        foreach ($carts as $cart) {
            $this->addCart($cart);
        }

        $this->collCarts = $carts;
        $this->collCartsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Cart objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Cart objects.
     * @throws PropelException
     */
    public function countCarts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCartsPartial && !$this->isNew();
        if (null === $this->collCarts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCarts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCarts());
            }

            $query = ChildCartQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUsers($this)
                ->count($con);
        }

        return count($this->collCarts);
    }

    /**
     * Method called to associate a ChildCart object to this object
     * through the ChildCart foreign key attribute.
     *
     * @param  ChildCart $l ChildCart
     * @return $this|\Users The current object (for fluent API support)
     */
    public function addCart(ChildCart $l)
    {
        if ($this->collCarts === null) {
            $this->initCarts();
            $this->collCartsPartial = true;
        }

        if (!$this->collCarts->contains($l)) {
            $this->doAddCart($l);

            if ($this->cartsScheduledForDeletion and $this->cartsScheduledForDeletion->contains($l)) {
                $this->cartsScheduledForDeletion->remove($this->cartsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildCart $cart The ChildCart object to add.
     */
    protected function doAddCart(ChildCart $cart)
    {
        $this->collCarts[]= $cart;
        $cart->setUsers($this);
    }

    /**
     * @param  ChildCart $cart The ChildCart object to remove.
     * @return $this|ChildUsers The current object (for fluent API support)
     */
    public function removeCart(ChildCart $cart)
    {
        if ($this->getCarts()->contains($cart)) {
            $pos = $this->collCarts->search($cart);
            $this->collCarts->remove($pos);
            if (null === $this->cartsScheduledForDeletion) {
                $this->cartsScheduledForDeletion = clone $this->collCarts;
                $this->cartsScheduledForDeletion->clear();
            }
            $this->cartsScheduledForDeletion[]= clone $cart;
            $cart->setUsers(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Users is new, it will return
     * an empty collection; or if this Users has previously
     * been saved, it will retrieve related Carts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Users.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCart[] List of ChildCart objects
     */
    public function getCartsJoinProducts(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCartQuery::create(null, $criteria);
        $query->joinWith('Products', $joinBehavior);

        return $this->getCarts($query, $con);
    }

    /**
     * Clears out the collMailingaddresses collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addMailingaddresses()
     */
    public function clearMailingaddresses()
    {
        $this->collMailingaddresses = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collMailingaddresses collection loaded partially.
     */
    public function resetPartialMailingaddresses($v = true)
    {
        $this->collMailingaddressesPartial = $v;
    }

    /**
     * Initializes the collMailingaddresses collection.
     *
     * By default this just sets the collMailingaddresses collection to an empty array (like clearcollMailingaddresses());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initMailingaddresses($overrideExisting = true)
    {
        if (null !== $this->collMailingaddresses && !$overrideExisting) {
            return;
        }

        $collectionClassName = MailingaddressTableMap::getTableMap()->getCollectionClassName();

        $this->collMailingaddresses = new $collectionClassName;
        $this->collMailingaddresses->setModel('\Mailingaddress');
    }

    /**
     * Gets an array of ChildMailingaddress objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUsers is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildMailingaddress[] List of ChildMailingaddress objects
     * @throws PropelException
     */
    public function getMailingaddresses(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collMailingaddressesPartial && !$this->isNew();
        if (null === $this->collMailingaddresses || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collMailingaddresses) {
                // return empty collection
                $this->initMailingaddresses();
            } else {
                $collMailingaddresses = ChildMailingaddressQuery::create(null, $criteria)
                    ->filterByUsers($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collMailingaddressesPartial && count($collMailingaddresses)) {
                        $this->initMailingaddresses(false);

                        foreach ($collMailingaddresses as $obj) {
                            if (false == $this->collMailingaddresses->contains($obj)) {
                                $this->collMailingaddresses->append($obj);
                            }
                        }

                        $this->collMailingaddressesPartial = true;
                    }

                    return $collMailingaddresses;
                }

                if ($partial && $this->collMailingaddresses) {
                    foreach ($this->collMailingaddresses as $obj) {
                        if ($obj->isNew()) {
                            $collMailingaddresses[] = $obj;
                        }
                    }
                }

                $this->collMailingaddresses = $collMailingaddresses;
                $this->collMailingaddressesPartial = false;
            }
        }

        return $this->collMailingaddresses;
    }

    /**
     * Sets a collection of ChildMailingaddress objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $mailingaddresses A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildUsers The current object (for fluent API support)
     */
    public function setMailingaddresses(Collection $mailingaddresses, ConnectionInterface $con = null)
    {
        /** @var ChildMailingaddress[] $mailingaddressesToDelete */
        $mailingaddressesToDelete = $this->getMailingaddresses(new Criteria(), $con)->diff($mailingaddresses);


        $this->mailingaddressesScheduledForDeletion = $mailingaddressesToDelete;

        foreach ($mailingaddressesToDelete as $mailingaddressRemoved) {
            $mailingaddressRemoved->setUsers(null);
        }

        $this->collMailingaddresses = null;
        foreach ($mailingaddresses as $mailingaddress) {
            $this->addMailingaddress($mailingaddress);
        }

        $this->collMailingaddresses = $mailingaddresses;
        $this->collMailingaddressesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Mailingaddress objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Mailingaddress objects.
     * @throws PropelException
     */
    public function countMailingaddresses(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collMailingaddressesPartial && !$this->isNew();
        if (null === $this->collMailingaddresses || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collMailingaddresses) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getMailingaddresses());
            }

            $query = ChildMailingaddressQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUsers($this)
                ->count($con);
        }

        return count($this->collMailingaddresses);
    }

    /**
     * Method called to associate a ChildMailingaddress object to this object
     * through the ChildMailingaddress foreign key attribute.
     *
     * @param  ChildMailingaddress $l ChildMailingaddress
     * @return $this|\Users The current object (for fluent API support)
     */
    public function addMailingaddress(ChildMailingaddress $l)
    {
        if ($this->collMailingaddresses === null) {
            $this->initMailingaddresses();
            $this->collMailingaddressesPartial = true;
        }

        if (!$this->collMailingaddresses->contains($l)) {
            $this->doAddMailingaddress($l);

            if ($this->mailingaddressesScheduledForDeletion and $this->mailingaddressesScheduledForDeletion->contains($l)) {
                $this->mailingaddressesScheduledForDeletion->remove($this->mailingaddressesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildMailingaddress $mailingaddress The ChildMailingaddress object to add.
     */
    protected function doAddMailingaddress(ChildMailingaddress $mailingaddress)
    {
        $this->collMailingaddresses[]= $mailingaddress;
        $mailingaddress->setUsers($this);
    }

    /**
     * @param  ChildMailingaddress $mailingaddress The ChildMailingaddress object to remove.
     * @return $this|ChildUsers The current object (for fluent API support)
     */
    public function removeMailingaddress(ChildMailingaddress $mailingaddress)
    {
        if ($this->getMailingaddresses()->contains($mailingaddress)) {
            $pos = $this->collMailingaddresses->search($mailingaddress);
            $this->collMailingaddresses->remove($pos);
            if (null === $this->mailingaddressesScheduledForDeletion) {
                $this->mailingaddressesScheduledForDeletion = clone $this->collMailingaddresses;
                $this->mailingaddressesScheduledForDeletion->clear();
            }
            $this->mailingaddressesScheduledForDeletion[]= clone $mailingaddress;
            $mailingaddress->setUsers(null);
        }

        return $this;
    }

    /**
     * Clears out the collTransactionss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addTransactionss()
     */
    public function clearTransactionss()
    {
        $this->collTransactionss = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collTransactionss collection loaded partially.
     */
    public function resetPartialTransactionss($v = true)
    {
        $this->collTransactionssPartial = $v;
    }

    /**
     * Initializes the collTransactionss collection.
     *
     * By default this just sets the collTransactionss collection to an empty array (like clearcollTransactionss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initTransactionss($overrideExisting = true)
    {
        if (null !== $this->collTransactionss && !$overrideExisting) {
            return;
        }

        $collectionClassName = TransactionsTableMap::getTableMap()->getCollectionClassName();

        $this->collTransactionss = new $collectionClassName;
        $this->collTransactionss->setModel('\Transactions');
    }

    /**
     * Gets an array of ChildTransactions objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUsers is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildTransactions[] List of ChildTransactions objects
     * @throws PropelException
     */
    public function getTransactionss(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collTransactionssPartial && !$this->isNew();
        if (null === $this->collTransactionss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collTransactionss) {
                // return empty collection
                $this->initTransactionss();
            } else {
                $collTransactionss = ChildTransactionsQuery::create(null, $criteria)
                    ->filterByUsers($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collTransactionssPartial && count($collTransactionss)) {
                        $this->initTransactionss(false);

                        foreach ($collTransactionss as $obj) {
                            if (false == $this->collTransactionss->contains($obj)) {
                                $this->collTransactionss->append($obj);
                            }
                        }

                        $this->collTransactionssPartial = true;
                    }

                    return $collTransactionss;
                }

                if ($partial && $this->collTransactionss) {
                    foreach ($this->collTransactionss as $obj) {
                        if ($obj->isNew()) {
                            $collTransactionss[] = $obj;
                        }
                    }
                }

                $this->collTransactionss = $collTransactionss;
                $this->collTransactionssPartial = false;
            }
        }

        return $this->collTransactionss;
    }

    /**
     * Sets a collection of ChildTransactions objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $transactionss A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildUsers The current object (for fluent API support)
     */
    public function setTransactionss(Collection $transactionss, ConnectionInterface $con = null)
    {
        /** @var ChildTransactions[] $transactionssToDelete */
        $transactionssToDelete = $this->getTransactionss(new Criteria(), $con)->diff($transactionss);


        $this->transactionssScheduledForDeletion = $transactionssToDelete;

        foreach ($transactionssToDelete as $transactionsRemoved) {
            $transactionsRemoved->setUsers(null);
        }

        $this->collTransactionss = null;
        foreach ($transactionss as $transactions) {
            $this->addTransactions($transactions);
        }

        $this->collTransactionss = $transactionss;
        $this->collTransactionssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Transactions objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Transactions objects.
     * @throws PropelException
     */
    public function countTransactionss(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collTransactionssPartial && !$this->isNew();
        if (null === $this->collTransactionss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTransactionss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getTransactionss());
            }

            $query = ChildTransactionsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUsers($this)
                ->count($con);
        }

        return count($this->collTransactionss);
    }

    /**
     * Method called to associate a ChildTransactions object to this object
     * through the ChildTransactions foreign key attribute.
     *
     * @param  ChildTransactions $l ChildTransactions
     * @return $this|\Users The current object (for fluent API support)
     */
    public function addTransactions(ChildTransactions $l)
    {
        if ($this->collTransactionss === null) {
            $this->initTransactionss();
            $this->collTransactionssPartial = true;
        }

        if (!$this->collTransactionss->contains($l)) {
            $this->doAddTransactions($l);

            if ($this->transactionssScheduledForDeletion and $this->transactionssScheduledForDeletion->contains($l)) {
                $this->transactionssScheduledForDeletion->remove($this->transactionssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildTransactions $transactions The ChildTransactions object to add.
     */
    protected function doAddTransactions(ChildTransactions $transactions)
    {
        $this->collTransactionss[]= $transactions;
        $transactions->setUsers($this);
    }

    /**
     * @param  ChildTransactions $transactions The ChildTransactions object to remove.
     * @return $this|ChildUsers The current object (for fluent API support)
     */
    public function removeTransactions(ChildTransactions $transactions)
    {
        if ($this->getTransactionss()->contains($transactions)) {
            $pos = $this->collTransactionss->search($transactions);
            $this->collTransactionss->remove($pos);
            if (null === $this->transactionssScheduledForDeletion) {
                $this->transactionssScheduledForDeletion = clone $this->collTransactionss;
                $this->transactionssScheduledForDeletion->clear();
            }
            $this->transactionssScheduledForDeletion[]= clone $transactions;
            $transactions->setUsers(null);
        }

        return $this;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        $this->id = null;
        $this->first_name = null;
        $this->last_name = null;
        $this->email = null;
        $this->password_hash = null;
        $this->paypal_email = null;
        $this->join_date = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collCarts) {
                foreach ($this->collCarts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collMailingaddresses) {
                foreach ($this->collMailingaddresses as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collTransactionss) {
                foreach ($this->collTransactionss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collCarts = null;
        $this->collMailingaddresses = null;
        $this->collTransactionss = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(UsersTableMap::DEFAULT_STRING_FORMAT);
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preSave')) {
            return parent::preSave($con);
        }
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postSave')) {
            parent::postSave($con);
        }
    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preInsert')) {
            return parent::preInsert($con);
        }
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postInsert')) {
            parent::postInsert($con);
        }
    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preUpdate')) {
            return parent::preUpdate($con);
        }
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postUpdate')) {
            parent::postUpdate($con);
        }
    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preDelete')) {
            return parent::preDelete($con);
        }
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postDelete')) {
            parent::postDelete($con);
        }
    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
