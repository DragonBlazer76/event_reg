package com.example.ewerkzeventapp;

import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.ExecutionException;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.google.zxing.integration.android.IntentIntegrator;
import com.google.zxing.integration.android.IntentResult;
import com.example.ewerkzeventapp.JSONParser;

public class MainActivity extends Activity implements OnClickListener {
	final Context context = this;
	
	//URL to get JSON Array
    private static String urlget = "http://ewerkzlb-2018281668.ap-southeast-1.elb.amazonaws.com/sbeapp/web/geteventdetails";
    private static String urlpost = "http://ewerkzlb-2018281668.ap-southeast-1.elb.amazonaws.com/sbeapp/web/setgueststatus";
    private static String urllogout = "http://ewerkzlb-2018281668.ap-southeast-1.elb.amazonaws.com/sbeapp/web/setguestlogout";
	
	private Button scanBtn;
	private TextView formatTxt, contentTxt, eventidTxt, eventnameTxt, tablenoTxt;
	private Spinner spinner1;
	private String sSelectedItem;
	private String sScanString;
	private String guestname, tableno;
	
	//JSON Node Name
	private static final String TAG_EVENT = "event";
    private static final String TAG_EVENTID = "id";
    private static final String TAG_EVENTNAME = "name";
    private static final String TAG_RESPONSE = "response";

    private List<String> list = new ArrayList<String>();
    private List<String> listidx = new ArrayList<String>();
    

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		
		scanBtn = (Button)findViewById(R.id.scan_button);
		formatTxt = (TextView)findViewById(R.id.scan_format);
		contentTxt = (TextView)findViewById(R.id.scan_content);
		
		eventidTxt = (TextView)findViewById(R.id.event_id);
		eventnameTxt = (TextView)findViewById(R.id.event_name);
		
		tablenoTxt = (TextView)findViewById(R.id.table_no);
		
		scanBtn.setOnClickListener(this);
		
		new GetEvents().execute();

        
	}
	
	public void onClick(View v){
		//respond to clicks
		if(v.getId()==R.id.scan_button){
			//scan
			IntentIntegrator scanIntegrator = new IntentIntegrator(this);
			scanIntegrator.initiateScan();
		}
	}
	
	public void onActivityResult(int requestCode, int resultCode, Intent intent) {
		//retrieve scan result
		IntentResult scanningResult = IntentIntegrator.parseActivityResult(requestCode, resultCode, intent);
		if (scanningResult != null) {
			//we have a result
			String scanContent = scanningResult.getContents();
			//String scanFormat = scanningResult.getFormatName();
			//formatTxt.setText("FORMAT: " + scanFormat);
			
			sScanString = scanContent;
			
			try {
				String sResp = new PostEvents().execute().get();
				//true|[{"gname":"test 123 user","tableno":"12"}]
				if (sResp.contains("true|")) {
					tableno = sResp.substring(sResp.indexOf("tableno\":") + 9, sResp.indexOf("}]"));
					if (tableno.contains("null") || tableno.isEmpty() || tableno.contains("\"\"")) {
						tablenoTxt.setText("");
					}
					else {
						tableno = tableno.substring(1, tableno.length() - 1);
						tablenoTxt.setText("Table number is " + tableno);
					}
						
					guestname = sResp.substring(sResp.indexOf("gname\":\"") + 8, sResp.indexOf("\","));
					contentTxt.setText("Guest " + guestname + " registered successfully!");
				}
				else if (sResp.compareToIgnoreCase("NA") == 0) {
					contentTxt.setText("Guest is not found! Please contact administrator for help.");
					tablenoTxt.setText("");
				}
				else if (sResp.compareToIgnoreCase("REG") == 0) {
					
					AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
							context);
			 
					// set title
					alertDialogBuilder.setTitle("Log out Guest " + guestname + " ?");
					
					// set dialog message
					alertDialogBuilder
						.setMessage("Guest " + guestname + " already registered. Log out?")
						.setCancelable(false)
						.setPositiveButton("Yes",new DialogInterface.OnClickListener() {
							public void onClick(DialogInterface dialog,int id) {
								// if this button is clicked, close
								// current activity
								try {
									String sLogoutRes = new PostLogoutEvents().execute().get();
									if (sLogoutRes.contains("true")) {
										contentTxt.setText("Guest " + guestname + " logged out successfully");
									}
									else {
										contentTxt.setText("Guest " + guestname + " not logged out.");
									}
									tablenoTxt.setText("");
								} catch (InterruptedException
										| ExecutionException e) {
									// TODO Auto-generated catch block
									e.printStackTrace();
								}
							}
						  })
						.setNegativeButton("No",new DialogInterface.OnClickListener() {
							public void onClick(DialogInterface dialog,int id) {
								// if this button is clicked, just close
								// the dialog box and do nothing
								contentTxt.setText("Guest " + guestname + "  not logged out.");
								tablenoTxt.setText("");
								dialog.cancel();
							}
						});
		 
						// create alert dialog
						AlertDialog alertDialog = alertDialogBuilder.create();
		 
						// show it
						alertDialog.show();
			 
				}
			} catch (InterruptedException | ExecutionException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
		else {
		    Toast toast = Toast.makeText(getApplicationContext(), 
		        "No scan data received!", Toast.LENGTH_SHORT);
		    toast.show();
		}
	}
	
	private void displayEventList() {
		spinner1 = (Spinner)findViewById(R.id.spn_event);
        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(getApplicationContext(), R.layout.spinner_item, list);
        dataAdapter.setDropDownViewResource(R.layout.spinner_dropdown_item);
        //dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinner1.setAdapter(dataAdapter);

        OnItemSelectedListener spnSelectedListener = new OnItemSelectedListener() {
        	 
            @Override
            public void onItemSelected(AdapterView<?> parent, View container,
                    int pos, long id) {
                //mTvCountry.setText(countries[position]);
            	sSelectedItem = listidx.get((int) parent.getItemIdAtPosition(pos));
            }
 
            @Override
            public void onNothingSelected(AdapterView<?> arg0) {
                // TODO Auto-generated method stub
            }
        };
 
        // Setting ItemClick Handler for Spinner Widget
        spinner1.setOnItemSelectedListener(spnSelectedListener);
	}
	
	private class GetEvents extends AsyncTask<String, String, String> {

		@Override
		protected String doInBackground(String... params) {
			// TODO Auto-generated method stub
			
			
			// Creating new JSON Parser
	        JSONParser jParser = new JSONParser();
	        // Getting JSON from URL
	        JSONObject json = null;
			json = jParser.getJSONFromUrl(urlget, "GET", "", "");

	       // Log.d("Response: ", "> " + json);
	        
	        try {
            // Getting JSON Array
            //user = json.getJSONArray(TAG_USER);
            //JSONObject c = user.getJSONObject(0);
 
            // Storing  JSON item in a Variable	
        	
        	JSONArray jArray = json.getJSONArray(TAG_EVENT);
        	
        	for(int i=0; i < jArray.length(); i++) {
        		JSONObject jObject = jArray.getJSONObject(i);
	            String id = jObject.getString(TAG_EVENTID);
	            String name = jObject.getString(TAG_EVENTNAME);
	            listidx.add(id);
	            list.add(name);
	            
	            //Set JSON Data in TextView
	            //eventidTxt.setText(id);
	            //eventnameTxt.setText(name);
        	}
 
	    } catch (JSONException e) {
	        e.printStackTrace();
	    }
	              
			return null;
		}
		
		@Override
		protected void onPostExecute(String result) {
			displayEventList();
		}

		
	}
	
	private class PostEvents extends AsyncTask<Void, Void, String> {

		@Override
		protected String doInBackground(Void... params) {
			// TODO Auto-generated method stub
			String res = "";
			JSONParser jParser = new JSONParser();
	        // Getting JSON from URL
	        JSONObject json = null;
			json = jParser.getJSONFromUrl(urlpost, "POST", sSelectedItem, sScanString);

	        Log.d("Response: ", "> " + json);
	        
	        try {
	        	//JSONArray jArray = json.getJSONArray(TAG_EVENT);
	        	JSONObject jObject = json.getJSONObject(TAG_EVENT);
	            res = jObject.getString(TAG_RESPONSE);
	        	
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			
			return res;
		}
		
	}
	
	private class PostLogoutEvents extends AsyncTask<Void, Void, String> {

		@Override
		protected String doInBackground(Void... params) {
			// TODO Auto-generated method stub
			String res = "";
			JSONParser jParser = new JSONParser();
	        // Getting JSON from URL
	        JSONObject json = null;
			json = jParser.postLogout(urllogout, "POST", sSelectedItem, sScanString);

	        Log.d("Response: ", "> " + json);
	        
	        try {
	        	//JSONArray jArray = json.getJSONArray(TAG_EVENT);
	        	JSONObject jObject = json.getJSONObject(TAG_EVENT);
	            res = jObject.getString(TAG_RESPONSE);
	        	
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			
			return res;
		}
		
	}

}
