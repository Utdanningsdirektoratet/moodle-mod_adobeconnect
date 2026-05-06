# Adobe Connect - A Moodle plugin

> _v. 2.0 2026/04/29_

## ❓ About this activity module

This plugin was previously maintained by [Remote Learner](https://github.com/remotelearner), but as it hasn't received any updates since the 14th of December 2017, Utdanningsdirektoratet has taken on its maintenance for their own Moodle instance by doing a fork.

The plugin started of as the first publicly available, and officially sponsored, integration method between Moodle and Adobe Acrobat Connect Pro as a partnership between Adobe Systems Inc. and Remote-Learner.net.

This integration is designed to simplify the use of synchronous events within Moodle. It provides a single-sign-on between the two systems with easy creation and management of Adobe Connect Pro meetings.

### About _Utdanningsdirektoratet_

> _Visit [udir.no](https://www.udir.no/) for more information._

_Utdanningsdirektoratet_ (The Norwegian Directorate for Education and Training) is a Norwegian government agency responsible for early childhood education, primary and secondary education.

The Directorate was established on June 15, 2004, and reports to the Ministry of Education and Research.

## 🛠️ Install instructions

> _Please see the documentation on [Moodle Docs](http://docs.moodle.org/en/Remote_Learner_Adobe_Connect_Pro_Module)._

Create a directory called `adobeconnect` in your `mod` directory, and copy all the files for this module into the `adobeconnect` directory. Log in to your Moodle site as an administrator and click on the _"notifications"_  link in the Adminsitration block and ensure all tables were setup correctly.

You will then be prompted to enter details about Adobe Connect Pro server. You may not see the _'Test Connection'_ button at first.  In the administrator block click on _**Modules**_ -> _**Activities**_ -> _**Adobe Connect**_ and you should now see the _'Test Connection'_ button.

Be sure to test your connection.

Once that is complete you can begin to create and administer meetings.

## 🗺 Want to contribute?

Found a bug that you want to inform us about, send in an [issue](https://github.com/Utdanningsdirektoratet/moodle-mod_adobeconnect/issues)!

Got a PR ready for an existing issue, feel free to [send it in](https://github.com/Utdanningsdirektoratet/moodle-mod_adobeconnect/pulls) for us to check out.

## 🌐 Need to contact us?

Check out our Discussions-section! Here you can post whatever you'd want to discuss with us, whether it's an idea/suggestion or a question you want us to answer.

## 📜 License

This plugin is licensed under [GNU GENERAL PUBLIC LICENSE v3](./LICENSE), except for specific file(s) which are licensed under the MIT License.

- [Cryptor.php](./classes/Cryptor.php) is licensed under the MIT License.