<h3>Personal Campaign Details</h3>

<table>
  <thead>
  <tr>
    <th>Title</th>
    <th>Status</th>
    <th>Contribution Page/Event</th>
    <th>No:of Contributions</th>
    <th>Amount Raised</th>
    <th>Goal Amount</th>
    <th style="min-width: 80px">Action</th>
  </tr>
  </thead>
  <tbody>
{foreach from=$campignResults key=myId item=i}
 	
  <tr>
    <td><a href="{$i.view_page_link}" target="_blank">{$i.title}</a></td>
    <td>{$i.is_active}</td>
    <td>{$i.page_type}</td>
    <td>{$i.no_of_contributions}</td>
    <td>{$i.amout_raised}</td>
    <td>{$i.goal_amount}</td>
    <td>
      <a href="{$i.edit_page_link}" target="_blank" target="_blank">Edit</a>      
    </td>
  </tr>
  {/foreach}
  </tbody>
</table>