# Yii2 - StateMachine Demo


This project demonstrates the use of the Yii2 State Machine. To install the demo on your local machine you need to
do the following.

1. Clone the project in some folder
2. Create a database with name `statemachinedemo`
3. run composer install `composer install` to get the dependencies
4. Install the database schema
```bash
./yii migrate && ./yii migrate --migrationPath='@vendor/ptheofan/yii2-statemachine/migrations/'
```
5. Configure your webserver to point to the `<projectfolder>/web` according to Yii2 documentation.
6. Navigate to the project on your local and play around.
7. Install graphviz (example for Ubuntu: sudo apt-get install graphviz)


## Yii2 StateMachine
The yii2-statemachine package is available at https://packagist.org/packages/ptheofan/yii2-statemachine
The sourcecode is available on github at https://github.com/ptheofan/yii2-statemachine


## Documentation
Work in progress


## Working Demo
You can see the demo in action at https://yii2-statemachine.com
