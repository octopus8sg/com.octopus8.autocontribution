# com.octopus8.autocontribution

This is a redo/revamp of my first extension, Auto Contribution.

Like the original, this extension creates a new activity type, Pending Contribution, that can be mapped onto Ninja Forms. When an activity with this activity type is marked as "Completed", a new contribution will be made with these fields.

New Features in this version
- More stable installation/enabling
- Mapping field

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.4+
* CiviCRM

## Things to know before use

> [!NOTE]
> TLDR: Do not touch the "Pending Contribution Fields" group at ALL, use the settings page instead and only edit the original fields.

- The mapping page will take a few seconds to load it's content, please wait.

- For extension fields like Amount and financial type, do not edit them at all. Deleting or modifying default fields can cause code conflicts, especially the Payment Method field, which will remove the system's Payment_Instrument option group.

- To clone a custom contribution field, use the extension's mapping page instead of manually adding it to "Pending Contribution Fields." You can add custom fields to the activity, but they won't be included in the generated contribution.

- When changing contribution field mappings, edit the original field, not the cloned one in "Pending Contribution Fields." Editing the original updates the cloned field, not vice versa

- Modify financial types in Settings > Civicontribute > Financial Types, not through the option group, which only lists existing types in the system.

- To delete cloned fields, delete it from "Pending Contribution Fields", it wasn't added to the settings page as it'll delete all records.

## How to use the mapping page

1. First in the Navigation Menu, hover over Contributions and then click on "Auto Contribution Settings"
![Screenshot](/images/Navigate.png)

You will appear on this page
![Screenshot](/images/Navigate2.png)

2. Under "Choose Field Group" input, choose the Custom Field Group where the field you wish to map is located (The group must be for Contributions)
![Screenshot](/images/Navigate3.png)

3. Then, select the field you wish to map and press "Add"
![Screenshot](/images/Navigate4.png)

4. The page should then refresh (It takes a few second) and presto!
![Screenshot](/images/Navigate5.png)

## Expected result

Inside the activity

![Screenshot](/images/pen.png)

Inside the contribution generated after the activity is marked "Completed"
![Screenshot](/images/pen2.png)

## Installation

TBA

## Getting Started

(* FIXME: Where would a new user navigate to get started? What changes would they see? *)

## Known Issues

- If the system tries to find the original field of a cloned field in "Pending Contribution Fields", it will refuse to load the settings page. It shouldn't happen as when the original field is deleted, it should delete the cloned field as well in "PCF". If this error occurs, please delete the cloned field whose original field does not exist (To find the original field, the name for the cloned field starts with "autocon_cloned_" followed by the id of the original.)